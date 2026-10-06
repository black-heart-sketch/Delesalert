<?php

namespace App\Services;

use App\Contracts\BillPaymentGateway;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class BillPaymentService
{
    public function __construct(private BillPaymentGateway $gateway) {}

    public function initiate(Bill $bill, User $user, string $customerPhone): BillPayment
    {
        [$payment, $shouldInitiate] = DB::transaction(function () use ($bill, $user, $customerPhone): array {
            $lockedBill = Bill::query()->lockForUpdate()->findOrFail($bill->id);
            abort_if($lockedBill->status === 'PAID', 422, 'This bill has already been paid.');

            $existingPayment = $lockedBill->payments()
                ->whereIn('status', ['INITIATING', 'PENDING', 'REVIEW'])
                ->first();

            if ($existingPayment) {
                return [$existingPayment, false];
            }

            return [BillPayment::create([
                'bill_id' => $lockedBill->id,
                'user_id' => $user->id,
                'amount' => $lockedBill->amount_due,
                'currency' => $lockedBill->currency,
                'provider' => mb_strtoupper((string) config('delestalert.payments.driver')),
                'status' => 'INITIATING',
                'transaction_reference' => 'DLST-'.Str::uuid(),
                'customer_phone' => $customerPhone,
            ]), true];
        });

        if (! $shouldInitiate) {
            return $payment;
        }

        try {
            $result = $this->gateway->initiate(
                $bill,
                $customerPhone,
                $user->email,
                $payment->transaction_reference,
            );

            if ($result['transaction_id'] === '') {
                throw new RuntimeException('The payment provider returned no transaction identifier.');
            }

            $payment->update([
                'provider_transaction_id' => $result['transaction_id'],
                'provider_payload' => $result['payload'],
                'status' => $result['status'],
            ]);

            if ($result['status'] === 'COMPLETED') {
                return $this->complete($payment, $result['payload']);
            }

            return $payment->fresh();
        } catch (Throwable $exception) {
            $payment->update([
                'status' => 'REVIEW',
                'failure_message' => 'The payment provider did not confirm whether this transaction was created.',
            ]);

            throw $exception;
        }
    }

    public function refresh(BillPayment $payment): BillPayment
    {
        if ($payment->status === 'COMPLETED') {
            return $payment;
        }

        if (! $payment->provider_transaction_id) {
            throw new RuntimeException('This payment has no provider transaction identifier.');
        }

        $result = $this->gateway->status($payment->provider_transaction_id);

        if ($result['transaction_id'] !== $payment->provider_transaction_id) {
            $payment->update(['status' => 'REVIEW', 'failure_message' => 'Provider transaction mismatch.']);

            throw new RuntimeException('The payment response could not be verified.');
        }

        if ($result['amount'] !== null && abs($result['amount'] - (float) $payment->amount) > 0.001) {
            $payment->update(['status' => 'REVIEW', 'failure_message' => 'Provider amount mismatch.']);

            throw new RuntimeException('The payment amount could not be verified.');
        }

        if ($result['status'] === 'COMPLETED') {
            return $this->complete($payment, $result['payload']);
        }

        $payment->update([
            'status' => $result['status'],
            'provider_payload' => $result['payload'],
            'failure_message' => $result['status'] === 'FAILED' ? 'The Mobile Money payment failed.' : null,
        ]);

        return $payment->fresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function complete(BillPayment $payment, array $payload): BillPayment
    {
        return DB::transaction(function () use ($payment, $payload): BillPayment {
            $lockedPayment = BillPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->status === 'COMPLETED' && $lockedPayment->paid_at) {
                return $lockedPayment;
            }

            $paidAt = now();
            $lockedPayment->update([
                'status' => 'COMPLETED',
                'provider_payload' => $payload,
                'failure_message' => null,
                'paid_at' => $paidAt,
            ]);
            $lockedPayment->bill()->update(['status' => 'PAID', 'paid_at' => $paidAt]);

            return $lockedPayment->fresh();
        });
    }
}
