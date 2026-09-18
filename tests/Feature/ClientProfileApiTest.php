<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_update_personal_information(): void
    {
        $client = User::factory()->create([
            'first_name' => 'Marie',
            'last_name' => 'Doe',
            'email' => 'marie@example.com',
            'role' => 'CLIENT',
        ]);
        Sanctum::actingAs($client);

        $this->patchJson('/api/profile', [
            'first_name' => 'Aline',
            'last_name' => 'Nana',
            'email' => 'aline@example.com',
            'phone' => '+237699000000',
            'role' => 'ADMIN',
            'status' => 'SUSPENDED',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Aline Nana')
            ->assertJsonPath('data.email', 'aline@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $client->id,
            'name' => 'Aline Nana',
            'role' => 'CLIENT',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_client_must_supply_current_password_to_change_it(): void
    {
        $client = User::factory()->create(['role' => 'CLIENT', 'password' => 'old-password']);
        Sanctum::actingAs($client);

        $this->patchJson('/api/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $client->fresh()->password));
    }

    public function test_client_can_change_password_and_deactivate_account(): void
    {
        $client = User::factory()->create(['role' => 'CLIENT', 'password' => 'old-password']);
        Sanctum::actingAs($client);

        $this->patchJson('/api/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertOk();

        $this->assertTrue(Hash::check('new-password', $client->fresh()->password));

        $this->deleteJson('/api/profile', ['password' => 'new-password'])->assertNoContent();

        $this->assertDatabaseHas('users', ['id' => $client->id, 'status' => 'DEACTIVATED']);
    }

    public function test_provider_cannot_access_client_resources(): void
    {
        $provider = User::factory()->create(['role' => 'PROVIDER']);
        Sanctum::actingAs($provider);

        $this->getJson('/api/profile')->assertForbidden();
        $this->getJson('/api/locations')->assertForbidden();
        $this->getJson('/api/notifications')->assertForbidden();
        $this->getJson('/api/predictions')->assertForbidden();
    }

    public function test_outage_history_requires_an_authenticated_client_or_admin(): void
    {
        $this->getJson('/api/outages/history')->assertUnauthorized();

        $client = User::factory()->create(['role' => 'CLIENT']);
        Sanctum::actingAs($client);

        $this->getJson('/api/outages/history')
            ->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }
}
