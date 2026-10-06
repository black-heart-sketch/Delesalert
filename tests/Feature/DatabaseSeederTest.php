<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_complete_and_can_be_seeded_more_than_once(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('zones', 6);
        $this->assertDatabaseCount('saved_locations', 3);
        $this->assertDatabaseCount('outages', 7);
        $this->assertDatabaseCount('incidents', 5);
        $this->assertDatabaseCount('outage_reports', 6);
        $this->assertDatabaseCount('predictions', 6);
        $this->assertDatabaseCount('community_posts', 4);
        $this->assertDatabaseCount('community_comments', 2);
        $this->assertDatabaseCount('community_reactions', 3);
        $this->assertDatabaseCount('bills', 4);
        $this->assertDatabaseCount('bill_payments', 2);
        $this->assertDatabaseCount('notification_preferences', 2);

        $this->assertDatabaseHas('users', ['email' => 'admin@delestalert.cm', 'role' => 'ADMIN', 'status' => 'ACTIVE']);
        $this->assertDatabaseHas('outages', ['title' => 'Défaillance du transformateur principal', 'status' => 'ONGOING']);
        $this->assertDatabaseHas('outage_reports', ['status' => 'PENDING']);
        $this->assertDatabaseHas('predictions', ['model_version' => 'demo-statistical-v2', 'risk_level' => 'CRITICAL']);
        $this->assertDatabaseHas('bills', ['account_reference' => 'DLST-000128', 'status' => 'UNPAID']);
        $this->assertCredentials(['email' => 'client@delestalert.cm', 'password' => 'password']);
    }
}
