<?php

namespace Tests\Feature;

use App\Contracts\BillPaymentGateway;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class DigiPayBillPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_initiate_a_digipay_mobile_money_payment(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        config()->set('services.digipay.key', 'dpk_test_key');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 12500]);
        $this->mock(BillPaymentGateway::class, function (MockInterface $mock) use ($bill, $user): void {
            $mock->shouldReceive('initiate')
                ->once()
                ->withArgs(fn (Bill $receivedBill, string $phone, string $email, string $reference): bool => $receivedBill->is($bill)
                    && $phone === '237699000000'
                    && $email === $user->email
                    && str_starts_with($reference, 'DLST-'))
                ->andReturn([
                    'transaction_id' => 'txn_digipay_123',
                    'status' => 'PENDING',
                    'payload' => ['transactionId' => 'txn_digipay_123', 'status' => 'pending'],
                ]);
        });

        $response = $this->actingAs($user)->post(route('bills.pay', $bill), [
            'phone' => '+237699000000',
        ]);

        $response->assertRedirect(route('bills.index'));
        $response->assertSessionHas('success', 'Mobile Money confirmation requested. Approve it on your phone, then check the status.');
        $this->assertDatabaseHas('bill_payments', [
            'bill_id' => $bill->id,
            'user_id' => $user->id,
            'amount' => 12500,
            'provider' => 'DIGIPAY',
            'status' => 'PENDING',
            'provider_transaction_id' => 'txn_digipay_123',
            'customer_phone' => '237699000000',
        ]);
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'UNPAID']);
    }

    public function test_successful_status_check_marks_payment_and_bill_as_paid(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 12500]);
        $payment = $this->pendingPayment($bill, $user);
        $this->mock(BillPaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('status')
                ->once()
                ->with('txn_digipay_123')
                ->andReturn([
                    'transaction_id' => 'txn_digipay_123',
                    'status' => 'COMPLETED',
                    'amount' => 12500.0,
                    'payload' => ['transactionId' => 'txn_digipay_123', 'status' => 'success', 'amount' => 12500],
                ]);
        });

        $response = $this->actingAs($user)->post(route('bill-payments.refresh', $payment));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Payment confirmed. Your bill is now paid.');
        $this->assertDatabaseHas('bill_payments', ['id' => $payment->id, 'status' => 'COMPLETED']);
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'PAID']);
        $this->assertNotNull($payment->fresh()->paid_at);
        $this->assertNotNull($bill->fresh()->paid_at);
    }

    public function test_amount_mismatch_places_payment_in_review_without_paying_bill(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 12500]);
        $payment = $this->pendingPayment($bill, $user);
        $this->mock(BillPaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('status')->once()->andReturn([
                'transaction_id' => 'txn_digipay_123',
                'status' => 'COMPLETED',
                'amount' => 5000.0,
                'payload' => ['transactionId' => 'txn_digipay_123', 'status' => 'success', 'amount' => 5000],
            ]);
        });

        $response = $this->actingAs($user)->post(route('bill-payments.refresh', $payment));

        $response->assertRedirect();
        $response->assertSessionHasErrors('payment');
        $this->assertDatabaseHas('bill_payments', ['id' => $payment->id, 'status' => 'REVIEW']);
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'UNPAID']);
    }

    public function test_client_cannot_refresh_another_clients_payment(): void
    {
        $owner = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $otherClient = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($owner)->create();
        $payment = $this->pendingPayment($bill, $owner);

        $this->actingAs($otherClient)
            ->post(route('bill-payments.refresh', $payment))
            ->assertNotFound();

        $this->assertDatabaseHas('bill_payments', ['id' => $payment->id, 'status' => 'PENDING']);
    }

    public function test_digipay_requires_a_valid_cameroon_mobile_number(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        config()->set('services.digipay.key', 'dpk_test_key');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create();

        $this->actingAs($user)
            ->post(route('bills.pay', $bill), ['phone' => '699000000'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('bill_payments', ['bill_id' => $bill->id]);
    }

    public function test_client_can_initiate_payment_through_the_api(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        config()->set('services.digipay.key', 'dpk_test_key');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 8000]);
        $this->mock(BillPaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('initiate')->once()->andReturn([
                'transaction_id' => 'txn_api_456',
                'status' => 'PENDING',
                'payload' => ['transactionId' => 'txn_api_456', 'status' => 'pending'],
            ]);
        });
        Sanctum::actingAs($user);

        $this->postJson('/api/bills/'.$bill->id.'/payments', ['phone' => '237677000000'])
            ->assertAccepted()
            ->assertJsonPath('data.status', 'PENDING')
            ->assertJsonPath('data.provider_transaction_id', 'txn_api_456')
            ->assertJsonMissingPath('data.provider_payload')
            ->assertJsonMissingPath('data.failure_message');

        $this->assertDatabaseHas('bill_payments', [
            'bill_id' => $bill->id,
            'provider_transaction_id' => 'txn_api_456',
            'status' => 'PENDING',
        ]);
    }

    public function test_uncertain_initiation_is_held_for_review_and_cannot_be_retried(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        config()->set('services.digipay.key', 'dpk_test_key');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        $bill = Bill::factory()->for($user)->create(['amount_due' => 15000]);
        $this->mock(BillPaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('initiate')->once()->andThrow(new RuntimeException('Connection timed out.'));
        });

        $this->actingAs($user)
            ->post(route('bills.pay', $bill), ['phone' => '237699000000'])
            ->assertSessionHasErrors('payment');

        $this->actingAs($user)
            ->post(route('bills.pay', $bill), ['phone' => '237699000000'])
            ->assertSessionHas('success', 'This payment requires verification. Do not initiate another payment.');

        $this->assertDatabaseCount('bill_payments', 1);
        $this->assertDatabaseHas('bill_payments', ['bill_id' => $bill->id, 'status' => 'REVIEW']);
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'UNPAID']);
    }

    public function test_bill_checkout_renders_in_french(): void
    {
        config()->set('delestalert.payments.driver', 'digipay');
        $user = User::factory()->create(['role' => 'CLIENT', 'status' => 'ACTIVE']);
        Bill::factory()->for($user)->create();

        $this->actingAs($user)
            ->withSession(['locale' => 'fr'])
            ->get(route('bills.index'))
            ->assertSeeText('Payer')
            ->assertSeeText('Numéro Mobile Money')
            ->assertSeeText('DelestAlert ne demande jamais votre code PIN Mobile Money.');
    }

    private function pendingPayment(Bill $bill, User $user): BillPayment
    {
        return BillPayment::create([
            'bill_id' => $bill->id,
            'user_id' => $user->id,
            'amount' => $bill->amount_due,
            'currency' => 'XAF',
            'provider' => 'DIGIPAY',
            'status' => 'PENDING',
            'transaction_reference' => 'DLST-local-123',
            'provider_transaction_id' => 'txn_digipay_123',
            'customer_phone' => '237699000000',
        ]);
    }
}
