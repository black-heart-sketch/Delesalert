<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\User;
use App\Models\Zone;
use App\Services\PredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_openrouter_prediction_uses_aggregated_recorded_data(): void
    {
        $this->travelTo('2026-10-05 12:00:00');
        config()->set('delestalert.ai.driver', 'openrouter');
        config()->set('delestalert.ai.openrouter_key', 'test-key');
        config()->set('delestalert.ai.model', 'nvidia/nemotron-3-ultra-550b-a55b:free');
        $zone = $this->zoneWithHistoricalData();
        Http::preventStrayRequests();
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => "```json\n{\"probability\":0.72,\"duration_minutes\":95,\"confidence\":0.81,\"start_in_hours\":6,\"factors\":[\"Fréquence récente des coupures\",\"Incidents graves récents\"]}\n```",
                    ],
                ]],
            ]),
        ]);

        $prediction = app(PredictionService::class)->generate($zone);

        $this->assertSame(0.72, $prediction->probability);
        $this->assertSame(95, $prediction->estimated_duration_minutes);
        $this->assertSame(0.81, $prediction->confidence_score);
        $this->assertSame('HIGH', $prediction->risk_level);
        $this->assertTrue($prediction->ai_generated);
        $this->assertSame('nvidia/nemotron-3-ultra-550b-a55b:free', $prediction->model_version);
        $this->assertSame('2026-10-05 18:00:00', $prediction->predicted_start->toDateTimeString());
        $this->assertSame('2026-10-05 19:35:00', $prediction->predicted_end->toDateTimeString());
        $this->assertSame(1, $prediction->input_metrics['outages_90d']);
        $this->assertSame(1, $prediction->input_metrics['severe_incidents_30d']);
        $this->assertSame(1, $prediction->input_metrics['validated_reports_14d']);
        $this->assertSame(['Fréquence récente des coupures', 'Incidents graves récents'], $prediction->factors);
        Http::assertSent(function (Request $request): bool {
            $payload = $request->data();
            $userContent = json_decode($payload['messages'][1]['content'], true, 512, JSON_THROW_ON_ERROR);

            return $request->hasHeader('Authorization')
                && $payload['model'] === 'nvidia/nemotron-3-ultra-550b-a55b:free'
                && $payload['reasoning']['effort'] === 'low'
                && $payload['max_tokens'] === 1500
                && str_contains($payload['messages'][0]['content'], 'written in French')
                && $userContent['metrics']['outages_90d'] === 1
                && ! str_contains($payload['messages'][1]['content'], 'Customer private report');
        });
    }

    public function test_invalid_openrouter_output_uses_statistical_fallback(): void
    {
        config()->set('delestalert.ai.driver', 'openrouter');
        config()->set('delestalert.ai.openrouter_key', 'test-key');
        $zone = $this->zoneWithHistoricalData();
        Http::preventStrayRequests();
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => '{"probability": 4.2}']]],
            ]),
        ]);

        $prediction = app(PredictionService::class)->generate($zone);

        $this->assertFalse($prediction->ai_generated);
        $this->assertSame('statistical-baseline-v2', $prediction->model_version);
        $this->assertGreaterThanOrEqual(0, $prediction->probability);
        $this->assertLessThanOrEqual(1, $prediction->probability);
        $this->assertContains('Fréquence récente des coupures', $prediction->factors);
        Http::assertSentCount(1);
    }

    public function test_prediction_command_can_generate_for_one_zone_without_openrouter(): void
    {
        config()->set('delestalert.ai.driver', 'statistical');
        $zone = $this->zoneWithHistoricalData();
        Http::preventStrayRequests();

        $this->artisan('predictions:generate', ['--zone' => $zone->id])
            ->expectsOutputToContain($zone->name)
            ->expectsOutput('1 prediction(s) generated.')
            ->assertSuccessful();

        $this->assertDatabaseHas('predictions', [
            'zone_id' => $zone->id,
            'model_version' => 'statistical-baseline-v2',
            'ai_generated' => false,
        ]);
        Http::assertNothingSent();
    }

    private function zoneWithHistoricalData(): Zone
    {
        $zone = Zone::create([
            'name' => 'Bonamoussadi',
            'region' => 'Littoral',
            'city' => 'Douala',
            'latitude' => 4.09,
            'longitude' => 9.75,
        ]);
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $client = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        Outage::create([
            'zone_id' => $zone->id,
            'created_by' => $provider->id,
            'title' => 'Historical outage',
            'type' => 'UNPLANNED',
            'status' => 'RESOLVED',
            'source' => 'PROVIDER',
            'estimated_duration_minutes' => 180,
        ]);
        Incident::create([
            'provider_id' => $provider->id,
            'zone_id' => $zone->id,
            'title' => 'Transformer incident',
            'description' => 'Recorded operational incident.',
            'incident_type' => 'TECHNICAL_FAILURE',
            'severity' => 'HIGH',
            'status' => 'RESOLVED',
            'occurred_at' => now()->subDays(2),
        ]);
        OutageReport::create([
            'user_id' => $client->id,
            'zone_id' => $zone->id,
            'description' => 'Customer private report',
            'latitude' => 4.09,
            'longitude' => 9.75,
            'status' => 'VALIDATED',
            'reported_at' => now()->subDay(),
        ]);

        return $zone;
    }
}
