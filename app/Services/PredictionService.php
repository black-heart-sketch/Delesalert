<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class PredictionService
{
    public function generate(Zone $zone): Prediction
    {
        $metrics = $this->metricsFor($zone);
        $baseline = $this->baseline($metrics);
        $aiResult = $this->openRouterPrediction($zone, $metrics);
        $result = $aiResult ?? $baseline;
        $predictedStart = now()->addHours($result['start_in_hours']);
        $probabilityPercent = $result['probability'] * 100;

        return Prediction::create([
            'zone_id' => $zone->id,
            'predicted_start' => $predictedStart,
            'predicted_end' => $predictedStart->copy()->addMinutes($result['duration_minutes']),
            'estimated_duration_minutes' => $result['duration_minutes'],
            'probability' => $result['probability'],
            'risk_level' => $this->riskLevel($probabilityPercent),
            'confidence_score' => $result['confidence'],
            'input_metrics' => $metrics,
            'factors' => $result['factors'],
            'ai_generated' => $aiResult !== null,
            'model_version' => $aiResult !== null
                ? (string) config('delestalert.ai.model')
                : 'statistical-baseline-v2',
            'generated_at' => now(),
        ]);
    }

    /** @return array<string, int|float> */
    private function metricsFor(Zone $zone): array
    {
        $outages90Days = Outage::query()->whereBelongsTo($zone)->where('created_at', '>=', now()->subDays(90));
        $incidents30Days = Incident::query()->whereBelongsTo($zone)->where('occurred_at', '>=', now()->subDays(30));
        $reports14Days = OutageReport::query()->whereBelongsTo($zone)->where('reported_at', '>=', now()->subDays(14));

        return [
            'outages_90d' => (clone $outages90Days)->count(),
            'outages_30d' => (clone $outages90Days)->where('created_at', '>=', now()->subDays(30))->count(),
            'resolved_outages_90d' => (clone $outages90Days)->where('status', 'RESOLVED')->count(),
            'average_duration_minutes' => round((float) ((clone $outages90Days)->whereNotNull('estimated_duration_minutes')->avg('estimated_duration_minutes') ?? 0), 1),
            'active_outages' => Outage::query()->whereBelongsTo($zone)->where('status', 'ONGOING')->count(),
            'incidents_30d' => (clone $incidents30Days)->count(),
            'severe_incidents_30d' => (clone $incidents30Days)->whereIn('severity', ['HIGH', 'CRITICAL'])->count(),
            'reports_14d' => (clone $reports14Days)->count(),
            'validated_reports_14d' => (clone $reports14Days)->where('status', 'VALIDATED')->count(),
        ];
    }

    /** @return array{probability: float, duration_minutes: int, confidence: float, start_in_hours: int, factors: array<int, string>} */
    private function baseline(array $metrics): array
    {
        $probability = min(0.95, 0.08
            + ($metrics['outages_30d'] * 0.06)
            + ($metrics['incidents_30d'] * 0.07)
            + ($metrics['severe_incidents_30d'] * 0.05)
            + ($metrics['reports_14d'] * 0.025)
            + ($metrics['validated_reports_14d'] * 0.03));
        $sampleSize = $metrics['outages_90d'] + $metrics['incidents_30d'] + $metrics['reports_14d'];

        return [
            'probability' => round($probability, 4),
            'duration_minutes' => $metrics['average_duration_minutes'] > 0
                ? max(15, min(1440, (int) round($metrics['average_duration_minutes'])))
                : 120,
            'confidence' => round(min(0.90, 0.35 + ($sampleSize * 0.025)), 4),
            'start_in_hours' => 4,
            'factors' => $this->baselineFactors($metrics),
        ];
    }

    /** @return array{probability: float, duration_minutes: int, confidence: float, start_in_hours: int, factors: array<int, string>}|null */
    private function openRouterPrediction(Zone $zone, array $metrics): ?array
    {
        if (config('delestalert.ai.driver') !== 'openrouter' || blank(config('delestalert.ai.openrouter_key'))) {
            return null;
        }

        try {
            $response = Http::withToken(config('delestalert.ai.openrouter_key'))
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-OpenRouter-Title' => config('app.name', 'DelestAlert'),
                ])
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout((int) config('delestalert.ai.timeout', 45))
                ->post(config('delestalert.ai.url'), [
                    'model' => config('delestalert.ai.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a conservative electricity outage risk model. Use only the supplied aggregate historical metrics. Return one JSON object and no markdown or commentary. Do not claim certainty. Required fields: probability (number 0 to 1), duration_minutes (integer 15 to 1440), confidence (number 0 to 1), start_in_hours (integer 1 to 168), factors (array of 1 to 5 short, human-readable strings written in French).'],
                        ['role' => 'user', 'content' => json_encode([
                            'zone' => $zone->only(['name', 'city', 'region']),
                            'observation_windows' => ['outages_days' => 90, 'incidents_days' => 30, 'reports_days' => 14],
                            'metrics' => $metrics,
                        ], JSON_THROW_ON_ERROR)],
                    ],
                    'temperature' => 0.1,
                    'reasoning' => ['effort' => 'low'],
                    'max_tokens' => 1500,
                ])
                ->throw();

            $content = (string) data_get($response->json(), 'choices.0.message.content', '');
            $result = $this->validatedAiResult($content);

            if ($result === null) {
                Log::warning('OpenRouter returned an invalid outage prediction; statistical fallback used.', [
                    'zone_id' => $zone->id,
                    'finish_reason' => data_get($response->json(), 'choices.0.finish_reason'),
                    'content_length' => mb_strlen($content),
                ]);
            }

            return $result;
        } catch (Throwable $exception) {
            Log::warning('OpenRouter outage prediction failed; statistical fallback used.', [
                'zone_id' => $zone->id,
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    /** @return array{probability: float, duration_minutes: int, confidence: float, start_in_hours: int, factors: array<int, string>}|null */
    private function validatedAiResult(string $content): ?array
    {
        try {
            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content) ?? $content;
            $firstBrace = strpos($content, '{');
            $lastBrace = strrpos($content, '}');

            if ($firstBrace === false || $lastBrace === false || $lastBrace <= $firstBrace) {
                return null;
            }

            $result = json_decode(substr($content, $firstBrace, $lastBrace - $firstBrace + 1), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        $probability = filter_var($result['probability'] ?? null, FILTER_VALIDATE_FLOAT);
        $duration = filter_var($result['duration_minutes'] ?? null, FILTER_VALIDATE_INT);
        $confidence = filter_var($result['confidence'] ?? null, FILTER_VALIDATE_FLOAT);
        $start = filter_var($result['start_in_hours'] ?? null, FILTER_VALIDATE_INT);
        $factors = collect($result['factors'] ?? [])
            ->filter(fn ($factor) => is_string($factor) && trim($factor) !== '')
            ->map(fn (string $factor) => mb_substr(trim($factor), 0, 160))
            ->take(5)
            ->values()
            ->all();

        if ($probability === false || $probability < 0 || $probability > 1
            || $duration === false || $duration < 15 || $duration > 1440
            || $confidence === false || $confidence < 0 || $confidence > 1
            || $start === false || $start < 1 || $start > 168
            || $factors === []) {
            return null;
        }

        return [
            'probability' => round($probability, 4),
            'duration_minutes' => $duration,
            'confidence' => round($confidence, 4),
            'start_in_hours' => $start,
            'factors' => $factors,
        ];
    }

    /** @return array<int, string> */
    private function baselineFactors(array $metrics): array
    {
        $factors = [];

        if ($metrics['outages_30d'] > 0) {
            $factors[] = 'Fréquence récente des coupures';
        }
        if ($metrics['severe_incidents_30d'] > 0) {
            $factors[] = 'Incidents électriques graves récents';
        }
        if ($metrics['reports_14d'] > 0) {
            $factors[] = 'Signalements récents de la communauté';
        }

        return $factors ?: ['Données récentes limitées sur les perturbations'];
    }

    private function riskLevel(float $probabilityPercent): string
    {
        return match (true) {
            $probabilityPercent >= config('delestalert.prediction.critical_threshold') => 'CRITICAL',
            $probabilityPercent >= config('delestalert.prediction.high_threshold') => 'HIGH',
            $probabilityPercent >= config('delestalert.prediction.medium_threshold') => 'MEDIUM',
            default => 'LOW',
        };
    }
}
