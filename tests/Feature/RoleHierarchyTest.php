<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_and_administrator_can_use_client_workspace_features(): void
    {
        $zone = Zone::query()->create([
            'name' => 'Akwa',
            'region' => 'Littoral',
            'city' => 'Douala',
            'latitude' => 4.0511,
            'longitude' => 9.7679,
        ]);

        foreach (['PROVIDER', 'ADMIN'] as $role) {
            $user = User::factory()->create(['role' => $role, 'status' => 'ACTIVE']);
            $bill = Bill::factory()->for($user)->create();

            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertSeeText('My locations')
                ->assertSeeText('Report outage')
                ->assertSeeText('Notifications')
                ->assertSeeText('My bills');

            $this->actingAs($user)->get(route('locations.index'))->assertOk();
            $this->actingAs($user)->get(route('reports.create'))->assertOk();
            $this->actingAs($user)->get(route('notifications.index'))->assertOk();
            $this->actingAs($user)->get(route('bills.index'))->assertOk();

            $this->actingAs($user)->post(route('locations.store'), [
                'label' => $role.' office',
                'address' => 'Akwa, Douala',
                'zone_id' => $zone->id,
                'latitude' => 4.0511,
                'longitude' => 9.7679,
                'is_primary' => true,
            ])->assertRedirect();

            $this->assertDatabaseHas('saved_locations', [
                'user_id' => $user->id,
                'label' => $role.' office',
            ]);

            $this->actingAs($user)->post(route('reports.store'), [
                'zone_id' => $zone->id,
                'description' => 'Power is unavailable near the office.',
                'address' => 'Akwa, Douala',
                'latitude' => 4.0511,
                'longitude' => 9.7679,
            ])->assertRedirect(route('dashboard'));

            $this->assertDatabaseHas('outage_reports', [
                'user_id' => $user->id,
                'status' => 'PENDING',
            ]);

            $this->actingAs($user)->post(route('bills.pay', $bill))->assertRedirect(route('bills.index'));

            $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'PAID']);
        }
    }
}
