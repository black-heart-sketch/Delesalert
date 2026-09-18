<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\CommunityPost;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityAndBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_publish_a_community_update(): void
    {
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $zone = Zone::query()->create([
            'name' => 'Bonapriso',
            'region' => 'Littoral',
            'city' => 'Douala',
            'latitude' => 4.0483,
            'longitude' => 9.7043,
        ]);

        $response = $this->actingAs($user)->post(route('community.store'), [
            'zone_id' => $zone->id,
            'category' => 'UPDATE',
            'title' => 'Power restored on Avenue de Gaulle',
            'body' => 'Electricity returned around 18:30 in our block.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('community_posts', [
            'user_id' => $user->id,
            'zone_id' => $zone->id,
            'title' => 'Power restored on Avenue de Gaulle',
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_guests_are_redirected_from_the_community(): void
    {
        $this->get(route('community.index'))->assertRedirect(route('login'));
    }

    public function test_community_page_renders_for_an_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);

        $this->actingAs($user)
            ->get(route('community.index'))
            ->assertOk()
            ->assertSeeText('Community updates');
    }

    public function test_client_can_record_a_demo_payment_for_their_bill(): void
    {
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 12500]);

        $response = $this->actingAs($user)->post(route('bills.pay', $bill));

        $response->assertRedirect(route('bills.index'));
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'PAID']);
        $this->assertDatabaseHas('bill_payments', [
            'bill_id' => $bill->id,
            'user_id' => $user->id,
            'amount' => 12500,
            'provider' => 'DEMO',
            'status' => 'COMPLETED',
        ]);
    }

    public function test_bill_centre_renders_for_the_bill_owner(): void
    {
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['description' => 'September electricity usage']);

        $this->actingAs($user)
            ->get(route('bills.index'))
            ->assertOk()
            ->assertSeeText('Your electricity bills')
            ->assertSeeText($bill->description);
    }

    public function test_client_cannot_pay_someone_elses_bill(): void
    {
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->create();

        $this->actingAs($user)->post(route('bills.pay', $bill))->assertNotFound();

        $this->assertDatabaseMissing('bill_payments', ['bill_id' => $bill->id]);
    }

    public function test_admin_can_hide_a_community_post(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN', 'status' => 'ACTIVE']);
        $post = CommunityPost::factory()->create();

        $this->actingAs($admin)->patch(route('community.hide', $post))->assertRedirect();

        $this->assertDatabaseHas('community_posts', ['id' => $post->id, 'status' => 'HIDDEN']);
    }
}
