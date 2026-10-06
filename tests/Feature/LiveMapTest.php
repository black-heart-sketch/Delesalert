<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_map_exposes_aggregated_zone_information(): void
    {
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $client = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $zone = Zone::query()->create([
            'name' => 'Bonamoussadi',
            'region' => 'Littoral',
            'city' => 'Douala',
            'district' => 'Douala V',
            'latitude' => 4.0932,
            'longitude' => 9.7538,
        ]);
        Outage::query()->create([
            'zone_id' => $zone->id,
            'created_by' => $provider->id,
            'title' => 'Coupure test',
            'type' => 'UNPLANNED',
            'status' => 'ONGOING',
            'source' => 'PROVIDER',
        ]);
        Incident::query()->create([
            'provider_id' => $provider->id,
            'zone_id' => $zone->id,
            'title' => 'Incident test',
            'description' => 'Incident pour la carte.',
            'incident_type' => 'TECHNICAL_FAILURE',
            'severity' => 'HIGH',
            'status' => 'OPEN',
            'occurred_at' => now(),
        ]);
        OutageReport::query()->create([
            'user_id' => $client->id,
            'zone_id' => $zone->id,
            'description' => 'Signalement récent.',
            'latitude' => 4.0932,
            'longitude' => 9.7538,
            'status' => 'PENDING',
            'reported_at' => now(),
        ]);
        Prediction::query()->create([
            'zone_id' => $zone->id,
            'predicted_start' => now()->addHours(3),
            'estimated_duration_minutes' => 120,
            'probability' => 0.78,
            'risk_level' => 'HIGH',
            'confidence_score' => 0.82,
            'model_version' => 'test-model',
            'generated_at' => now(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertViewIs('welcome')
            ->assertSeeText('Bonamoussadi')
            ->assertSee('data-live-map', false)
            ->assertViewHas('mapPayload', function (array $payload): bool {
                $zone = $payload['zones']->first();

                return $zone['activeOutages'] === 1
                    && $zone['openIncidents'] === 1
                    && $zone['recentReports'] === 1
                    && $zone['status'] === 'outage'
                    && $zone['prediction']['probability'] === 78;
            });
    }
}
