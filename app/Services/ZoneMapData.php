<?php

namespace App\Services;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class ZoneMapData
{
    /**
     * Build the operational map payload for all zones or a selected subset.
     *
     * @param  Collection<int, int>|null  $zoneIds
     * @return array{zones: Collection<int, array<string, mixed>>, labels: array<string, string>}
     */
    public function build(?Collection $zoneIds = null): array
    {
        $zones = Zone::query()
            ->select(['id', 'name', 'city', 'region', 'district', 'latitude', 'longitude'])
            ->when($zoneIds !== null, fn (Builder $query) => $query->whereIn('id', $zoneIds))
            ->withCount([
                'outages as active_outages_count' => fn (Builder $query) => $query->where('status', 'ONGOING'),
                'outages as planned_outages_count' => fn (Builder $query) => $query->where('status', 'PLANNED'),
                'incidents as open_incidents_count' => fn (Builder $query) => $query->whereIn('status', ['OPEN', 'IN_PROGRESS']),
                'reports as recent_reports_count' => fn (Builder $query) => $query->where('reported_at', '>=', now()->subDays(14)),
            ])
            ->with(['latestPrediction' => fn (HasOne $query) => $query->select([
                'predictions.id',
                'predictions.zone_id',
                'predictions.probability',
                'predictions.risk_level',
                'predictions.confidence_score',
                'predictions.predicted_start',
                'predictions.estimated_duration_minutes',
                'predictions.ai_generated',
            ])])
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->map(function (Zone $zone): array {
                $prediction = $zone->latestPrediction;

                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'city' => $zone->city,
                    'region' => $zone->region,
                    'district' => $zone->district,
                    'latitude' => $zone->latitude,
                    'longitude' => $zone->longitude,
                    'activeOutages' => $zone->active_outages_count,
                    'plannedOutages' => $zone->planned_outages_count,
                    'openIncidents' => $zone->open_incidents_count,
                    'recentReports' => $zone->recent_reports_count,
                    'status' => $this->statusFor($zone),
                    'prediction' => $prediction ? [
                        'probability' => (int) round($prediction->probability * 100),
                        'riskLevel' => $prediction->risk_level,
                        'confidence' => (int) round($prediction->confidence_score * 100),
                        'predictedStart' => $prediction->predicted_start?->translatedFormat('d M, H:i'),
                        'durationMinutes' => $prediction->estimated_duration_minutes,
                        'aiGenerated' => $prediction->ai_generated,
                    ] : null,
                    'outagesUrl' => route('outages.index', ['search' => $zone->name]),
                ];
            });

        return [
            'zones' => $zones,
            'labels' => [
                'activeOutages' => __('Active outages'),
                'plannedOutages' => __('Planned maintenance'),
                'openIncidents' => __('Open incidents'),
                'recentReports' => __('Recent reports'),
                'prediction' => __('Outage probability'),
                'confidence' => __('Confidence'),
                'viewOutages' => __('View zone outages'),
                'noPrediction' => __('No prediction available'),
            ],
        ];
    }

    private function statusFor(Zone $zone): string
    {
        if ($zone->active_outages_count > 0) {
            return 'outage';
        }

        if ($zone->planned_outages_count > 0) {
            return 'planned';
        }

        if (in_array($zone->latestPrediction?->risk_level, ['HIGH', 'CRITICAL'], true)) {
            return 'risk';
        }

        return 'stable';
    }
}
