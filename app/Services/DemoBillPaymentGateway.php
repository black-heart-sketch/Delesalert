<?php

namespace App\Services;

use App\Contracts\BillPaymentGateway;
use App\Models\Bill;
use Illuminate\Support\Str;

class DemoBillPaymentGateway implements BillPaymentGateway
{
    public function initiate(Bill $bill, string $customerPhone, string $customerEmail, string $localReference): array
    {
        $transactionId = 'DEMO-'.Str::upper(Str::random(12));

        return [
            'transaction_id' => $transactionId,
            'status' => 'COMPLETED',
            'payload' => ['transactionId' => $transactionId, 'status' => 'success'],
        ];
    }

    public function status(string $transactionId): array
    {
        return [
            'transaction_id' => $transactionId,
            'status' => 'COMPLETED',
            'amount' => null,
            'payload' => ['transactionId' => $transactionId, 'status' => 'success'],
        ];
    }
}
