<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DelestAlertApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_current_outages_endpoint_has_consistent_response(): void
    {
        $this->getJson('/api/outages/current')->assertOk()->assertJsonStructure(['success', 'data']);
    }

    public function test_public_homepage_renders_the_full_application_entry_point(): void
    {
        $this->get('/')
            ->assertSee('Clarity when the power goes out.')
            ->assertSee('Create account');
    }

    public function test_client_dashboard_redirects_guests_and_renders_for_an_authenticated_client(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));

        $client = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);

        $this->actingAs($client)
            ->get('/dashboard')
            ->assertSee('Your electricity dashboard')
            ->assertSee('My locations')
            ->assertDontSee('Map provider');
    }

    public function test_provider_dashboard_shows_provider_analytics_and_navigation(): void
    {
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);

        $this->actingAs($provider)
            ->get('/dashboard')
            ->assertSee('Provider workspace')
            ->assertSee('Open incidents')
            ->assertSee('Outage board')
            ->assertSee('My locations')
            ->assertSee('My bills');
    }

    public function test_admin_dashboard_shows_administration_and_client_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN', 'status' => 'ACTIVE']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertSee('Platform command center')
            ->assertSee('Map provider')
            ->assertSee('Report outage')
            ->assertSee('My bills');
    }

    public function test_admin_can_update_the_map_provider_from_the_web_settings_screen(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN', 'status' => 'ACTIVE']);

        $this->actingAs($admin)
            ->put('/admin/map-settings', ['provider' => 'google'])
            ->assertRedirect(route('admin.map-settings.edit'));

        $this->assertDatabaseHas('system_settings', ['key' => 'map_provider', 'value' => 'google']);
    }

    public function test_locale_switcher_renders_the_french_homepage(): void
    {
        $this->post('/locale', ['locale' => 'fr'])
            ->assertRedirect();

        $this->get('/')
            ->assertSee('De la clarté quand le courant est coupé.');
    }

    public function test_demo_login_signs_in_to_the_selected_seed_role_when_enabled(): void
    {
        config()->set('app.demo_login_enabled', true);
        $provider = User::factory()->create(['role' => 'PROVIDER', 'status' => 'ACTIVE']);

        $this->post('/demo-login', ['role' => 'PROVIDER'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($provider);
    }

    public function test_demo_login_is_unavailable_when_disabled(): void
    {
        config()->set('app.demo_login_enabled', false);

        $this->post('/demo-login', ['role' => 'CLIENT'])->assertNotFound();
    }

    public function test_only_admin_can_switch_map_provider(): void
    {
        $client = User::factory()->create(['role' => 'CLIENT']);
        Sanctum::actingAs($client);
        $this->putJson('/api/admin/map-configuration', ['provider' => 'google'])->assertForbidden();

        $admin = User::factory()->create(['role' => 'ADMIN']);
        Sanctum::actingAs($admin);
        $this->putJson('/api/admin/map-configuration', ['provider' => 'google'])->assertOk()->assertJsonPath('data.provider', 'google');
        $this->assertDatabaseHas('system_settings', ['key' => 'map_provider', 'value' => 'google']);
    }

    public function test_client_can_register_and_submit_a_report(): void
    {
        $this->postJson('/api/auth/register', ['first_name' => 'Marie', 'last_name' => 'Doe', 'email' => 'marie@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertCreated()->assertJsonStructure(['data' => ['token']]);
        $client = User::where('email', 'marie@example.com')->firstOrFail();
        $zone = Zone::create(['name' => 'Akwa', 'region' => 'Littoral', 'city' => 'Douala', 'latitude' => 4.05, 'longitude' => 9.70]);
        Sanctum::actingAs($client);
        $this->postJson('/api/reports', ['zone_id' => $zone->id, 'description' => 'No power since this morning.', 'latitude' => 4.05, 'longitude' => 9.70])->assertCreated()->assertJsonPath('data.status', 'PENDING');
    }
}
