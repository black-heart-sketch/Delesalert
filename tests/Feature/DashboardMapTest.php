<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Outage;
use App\Models\SavedLocation;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_dashboard_map_focuses_on_saved_zones_and_is_translated(): void
    {
        $client = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $savedZone = $this->createZone('Bonamoussadi', 'Douala', 4.0932, 9.7538);
        $this->createZone('Bastos', 'Yaoundé', 3.8851, 11.5214);
        SavedLocation::query()->create([
            'user_id' => $client->id,
            'zone_id' => $savedZone->id,
            'label' => 'Maison',
            'address' => 'Bonamoussadi, Douala',
            'latitude' => 4.0932,
            'longitude' => 9.7538,
        ]);

        $response = $this->actingAs($client)
            ->withSession(['locale' => 'fr'])
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('data-live-map', false)
            ->assertSeeText('Vos zones surveillées')
            ->assertSeeText('Carte du réseau')
            ->assertViewHas('mapPayload', fn (array $payload): bool => $payload['zones']->pluck('id')->all() === [$savedZone->id]);
    }

    public function test_provider_dashboard_map_contains_only_operational_zones(): void
    {
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $outageZone = $this->createZone('Akwa', 'Douala', 4.0511, 9.7679);
        $incidentZone = $this->createZone('Mvan', 'Yaoundé', 3.8189, 11.5297);
        $unassignedZone = $this->createZone('Mile 17', 'Buea', 4.1307, 9.2702);

        Outage::query()->create([
            'zone_id' => $outageZone->id,
            'created_by' => $provider->id,
            'title' => 'Coupure Akwa',
            'type' => 'UNPLANNED',
            'status' => 'ONGOING',
            'source' => 'PROVIDER',
        ]);
        Incident::query()->create([
            'provider_id' => $provider->id,
            'zone_id' => $incidentZone->id,
            'title' => 'Incident Mvan',
            'description' => 'Incident technique.',
            'incident_type' => 'TECHNICAL_FAILURE',
            'severity' => 'HIGH',
            'status' => 'OPEN',
            'occurred_at' => now(),
        ]);

        $response = $this->actingAs($provider)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('data-live-map', false)
            ->assertViewHas('mapPayload', function (array $payload) use ($incidentZone, $outageZone, $unassignedZone): bool {
                $zoneIds = $payload['zones']->pluck('id');

                return $zoneIds->contains($outageZone->id)
                    && $zoneIds->contains($incidentZone->id)
                    && ! $zoneIds->contains($unassignedZone->id);
            });
    }

    public function test_administrator_dashboard_map_contains_all_zones(): void
    {
        $administrator = User::factory()->create(['role' => 'ADMIN', 'status' => 'ACTIVE']);
        $this->createZone('Akwa', 'Douala', 4.0511, 9.7679);
        $this->createZone('Bastos', 'Yaoundé', 3.8851, 11.5214);

        $response = $this->actingAs($administrator)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('data-live-map', false)
            ->assertViewHas('mapPayload', fn (array $payload): bool => $payload['zones']->count() === 2);
    }

    private function createZone(string $name, string $city, float $latitude, float $longitude): Zone
    {
        return Zone::query()->create([
            'name' => $name,
            'region' => 'Test region',
            'city' => $city,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
