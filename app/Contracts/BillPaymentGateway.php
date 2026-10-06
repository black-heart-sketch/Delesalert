<?php

namespace App\Contracts;

use App\Models\Bill;

interface BillPaymentGateway
{
    /**
     * @return array{transaction_id: string, status: string, payload: array<string, mixed>}
     */
    public function initiate(Bill $bill, string $customerPhone, string $customerEmail, string $localReference): array;

    /**
     * @return array{transaction_id: string, status: string, amount: float|null, payload: array<string, mixed>}
     */
    public function status(string $transactionId): array;
}
