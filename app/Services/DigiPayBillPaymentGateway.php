<?php

namespace App\Services;

use App\Contracts\BillPaymentGateway;
use App\Models\Bill;
use DigiPay\DigiPay;

class DigiPayBillPaymentGateway implements BillPaymentGateway
{
    private ?DigiPay $client = null;

    public function __construct() {}

    public function initiate(Bill $bill, string $customerPhone, string $customerEmail, string $localReference): array
    {
        $response = $this->client()->payments->initiate(
            amount: (float) $bill->amount_due,
            customerPhone: $customerPhone,
            customerEmail: $customerEmail,
            metadata: [
                'billId' => $bill->id,
                'accountReference' => $bill->account_reference,
                'localReference' => $localReference,
            ],
        );

        return [
            'transaction_id' => (string) ($response['transactionId'] ?? ''),
            'status' => $this->normalizeStatus((string) ($response['status'] ?? 'pending')),
            'payload' => $response,
        ];
    }

    public function status(string $transactionId): array
    {
        $response = $this->client()->payments->getStatus($transactionId);

        return [
            'transaction_id' => (string) ($response['transactionId'] ?? $transactionId),
            'status' => $this->normalizeStatus((string) ($response['status'] ?? 'pending')),
            'amount' => isset($response['amount']) ? (float) $response['amount'] : null,
            'payload' => $response,
        ];
    }

    private function normalizeStatus(string $status): string
    {
        return match (mb_strtolower($status)) {
            'success', 'succeeded', 'completed', 'paid' => 'COMPLETED',
            'failed', 'cancelled', 'canceled', 'declined' => 'FAILED',
            default => 'PENDING',
        };
    }

    private function client(): DigiPay
    {
        $baseUrl = config('services.digipay.base_url');

        return $this->client ??= new DigiPay(
            apiKey: config('services.digipay.key'),
            environment: config('services.digipay.environment', 'production'),
            baseUrl: filled($baseUrl) ? $baseUrl : null,
            timeout: (int) config('services.digipay.timeout', 15),
        );
    }
}
