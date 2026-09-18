<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;

class PredictionService
{
    public function generate(Zone $zone): Prediction
    {
        $metrics = ['outages' => Outage::where('zone_id', $zone->id)->where('created_at', '>=', now()->subDays(90))->count(), 'incidents' => Incident::where('zone_id', $zone->id)->where('occurred_at', '>=', now()->subDays(30))->count(), 'reports' => OutageReport::where('zone_id', $zone->id)->where('reported_at', '>=', now()->subDays(14))->count()];
        $probability = min(.95, .12 + ($metrics['outages'] * .05) + ($metrics['incidents'] * .09) + ($metrics['reports'] * .04));
        if (config('delestalert.ai.driver') === 'openrouter' && config('delestalert.ai.openrouter_key')) {
            $probability = $this->enrichWithOpenRouter($zone, $metrics, $probability);
        }
        $percent = $probability * 100;
        $risk = $percent >= config('delestalert.prediction.critical_threshold') ? 'CRITICAL' : ($percent >= config('delestalert.prediction.high_threshold') ? 'HIGH' : ($percent >= config('delestalert.prediction.medium_threshold') ? 'MEDIUM' : 'LOW'));

        return Prediction::create(['zone_id' => $zone->id, 'predicted_start' => now()->addHours(4), 'predicted_end' => now()->addHours(6), 'estimated_duration_minutes' => 120, 'probability' => $probability, 'risk_level' => $risk, 'confidence_score' => min(.90, .40 + ($metrics['outages'] * .03)), 'model_version' => config('delestalert.ai.driver') === 'openrouter' ? 'openrouter-baseline-v1' : 'statistical-baseline-v1', 'generated_at' => now()]);
    }

    private function enrichWithOpenRouter(Zone $zone, array $metrics, float $fallback): float
    {
        try {
            $response = Http::withToken(config('delestalert.ai.openrouter_key'))->timeout(12)->post(config('delestalert.ai.url'), ['model' => config('delestalert.ai.model'), 'messages' => [['role' => 'system', 'content' => 'Return only a decimal probability between 0 and 1 for outage risk.'], ['role' => 'user', 'content' => json_encode(['zone' => $zone->only('name', 'city', 'region'), 'metrics' => $metrics])]], 'temperature' => 0]);
            $value = (float) preg_replace('/[^0-9.]/', '', data_get($response->json(), 'choices.0.message.content', ''));

            return $value > 0 && $value <= 1 ? $value : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }
}
