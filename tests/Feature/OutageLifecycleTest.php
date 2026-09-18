<?php

namespace Tests\Feature;

use App\Models\NotificationPreference;
use App\Models\Outage;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OutageLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_publication_notifies_only_affected_active_clients(): void
    {
        $zone = $this->createZone('Akwa');
        $otherZone = $this->createZone('Bastos', 'Yaoundé');
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $affectedClient = $this->createClientFollowing($zone);
        $this->createClientFollowing($otherZone);
        $inactiveClient = $this->createClientFollowing($zone, 'SUSPENDED');
        Sanctum::actingAs($provider);

        $response = $this->postJson('/api/outages', [
            'zone_id' => $zone->id,
            'title' => 'Transformer failure',
            'description' => 'Technical intervention in progress.',
            'type' => 'UNPLANNED',
            'status' => 'ONGOING',
            'source' => 'ADMIN',
        ])->assertCreated()
            ->assertJsonPath('data.source', 'PROVIDER')
            ->assertJsonPath('data.status', 'ONGOING');

        $outageId = $response->json('data.id');
        $this->assertDatabaseHas('outages', [
            'id' => $outageId,
            'created_by' => $provider->id,
            'source' => 'PROVIDER',
        ]);
        $actualStart = Outage::findOrFail($outageId)->actual_start;
        $this->assertNotNull($actualStart);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $affectedClient->id,
            'notifiable_type' => User::class,
        ]);
        $this->assertDatabaseMissing('notifications', ['notifiable_id' => $inactiveClient->id]);

        $this->patchJson('/api/outages/'.$outageId, ['status' => 'ONGOING'])->assertOk();

        $this->assertTrue($actualStart->equalTo(Outage::findOrFail($outageId)->actual_start));
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_scheduled_and_restoration_preferences_are_respected(): void
    {
        $zone = $this->createZone('Bonamoussadi');
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $client = $this->createClientFollowing($zone);
        NotificationPreference::create([
            'user_id' => $client->id,
            'scheduled_outage_alerts' => false,
            'restoration_alerts' => true,
        ]);
        Sanctum::actingAs($provider);

        $response = $this->postJson('/api/outages', [
            'zone_id' => $zone->id,
            'title' => 'Line maintenance',
            'type' => 'SCHEDULED',
            'status' => 'PLANNED',
            'source' => 'PROVIDER',
            'scheduled_start' => now()->addDay()->toISOString(),
        ])->assertCreated();

        $this->assertDatabaseCount('notifications', 0);

        $this->patchJson('/api/outages/'.$response->json('data.id'), ['status' => 'RESOLVED'])
            ->assertOk()
            ->assertJsonPath('data.status', 'RESOLVED');

        $this->assertDatabaseCount('notifications', 1);
        $this->assertNotNull(Outage::findOrFail($response->json('data.id'))->actual_end);
    }

    public function test_provider_cannot_update_another_providers_outage(): void
    {
        $zone = $this->createZone('Deido');
        $owner = User::factory()->create(['role' => 'PROVIDER']);
        $otherProvider = User::factory()->create(['role' => 'PROVIDER']);
        $outage = Outage::create([
            'zone_id' => $zone->id,
            'created_by' => $owner->id,
            'title' => 'Feeder interruption',
            'type' => 'UNPLANNED',
            'status' => 'ONGOING',
            'source' => 'PROVIDER',
        ]);
        Sanctum::actingAs($otherProvider);

        $this->patchJson('/api/outages/'.$outage->id, ['status' => 'RESOLVED'])->assertForbidden();

        $this->assertDatabaseHas('outages', ['id' => $outage->id, 'status' => 'ONGOING']);
    }

    public function test_provider_and_client_workspaces_are_rendered_in_french(): void
    {
        $provider = User::factory()->create(['role' => 'PROVIDER']);

        $this->actingAs($provider)
            ->withSession(['locale' => 'fr'])
            ->get('/manage/outages')
            ->assertSee('Publier et mettre à jour les coupures')
            ->assertSee('Gérer les coupures');

        $client = User::factory()->create(['role' => 'CLIENT']);

        $this->actingAs($client)
            ->withSession(['locale' => 'fr'])
            ->get('/notifications')
            ->assertSee('Alertes personnalisées')
            ->assertSee('Aucune notification pour le moment');
    }

    private function createZone(string $name, string $city = 'Douala'): Zone
    {
        return Zone::create([
            'name' => $name,
            'region' => $city === 'Douala' ? 'Littoral' : 'Centre',
            'city' => $city,
            'latitude' => 4.05,
            'longitude' => 9.70,
        ]);
    }

    private function createClientFollowing(Zone $zone, string $status = 'ACTIVE'): User
    {
        $client = User::factory()->create(['role' => 'CLIENT', 'status' => $status]);
        $client->locations()->create([
            'zone_id' => $zone->id,
            'label' => 'Home',
            'address' => $zone->name.', '.$zone->city,
            'latitude' => $zone->latitude,
            'longitude' => $zone->longitude,
            'is_primary' => true,
        ]);

        return $client;
    }
}
