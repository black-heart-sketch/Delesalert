<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bill_id', 'user_id', 'amount', 'currency', 'provider', 'status', 'transaction_reference', 'provider_transaction_id', 'customer_phone', 'provider_payload', 'failure_message', 'paid_at'])]
#[Hidden(['provider_payload', 'failure_message'])]
class BillPayment extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'provider_payload' => 'array', 'paid_at' => 'datetime'];
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
