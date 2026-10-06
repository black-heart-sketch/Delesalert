<?php

namespace Tests\Feature;

use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_complete_and_can_be_seeded_more_than_once(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 18);
        $this->assertDatabaseCount('zones', 24);
        $this->assertDatabaseCount('saved_locations', 27);
        $this->assertDatabaseCount('outages', 25);
        $this->assertDatabaseCount('incidents', 23);
        $this->assertDatabaseCount('outage_reports', 42);
        $this->assertDatabaseCount('predictions', 24);
        $this->assertDatabaseCount('community_posts', 22);
        $this->assertDatabaseCount('community_comments', 20);
        $this->assertDatabaseCount('community_reactions', 39);
        $this->assertDatabaseCount('bills', 52);
        $this->assertDatabaseCount('bill_payments', 38);
        $this->assertDatabaseCount('notification_preferences', 14);
        $this->assertDatabaseCount('notifications', 36);

        $this->assertDatabaseHas('users', ['email' => 'admin@delestalert.cm', 'role' => 'ADMIN', 'status' => 'ACTIVE']);
        $this->assertDatabaseHas('users', ['email' => 'provider.littoral@delestalert.cm', 'role' => 'PROVIDER', 'status' => 'ACTIVE']);
        $this->assertDatabaseHas('zones', ['name' => 'Domayo', 'city' => 'Maroua', 'region' => 'Extrême-Nord']);
        $this->assertDatabaseHas('outages', ['title' => 'Défaillance du transformateur principal', 'status' => 'ONGOING']);
        $this->assertDatabaseHas('outage_reports', ['status' => 'PENDING']);
        $this->assertDatabaseHas('predictions', ['model_version' => 'demo-statistical-v2', 'risk_level' => 'CRITICAL']);
        $this->assertDatabaseHas('predictions', ['model_version' => 'expanded-demo-v1', 'ai_generated' => true]);
        $this->assertDatabaseHas('bills', ['account_reference' => 'DLST-000128', 'status' => 'UNPAID']);
        $this->assertSame(10, Zone::query()->distinct()->count('region'));
        $this->assertCredentials(['email' => 'client@delestalert.cm', 'password' => 'password']);
    }
}
