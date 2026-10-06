<?php

namespace App\Models;

use Database\Factories\BillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'provider_name', 'account_reference', 'description', 'amount_due', 'currency', 'due_date', 'status', 'paid_at'])]
class Bill extends Model
{
    /** @use HasFactory<BillFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['amount_due' => 'decimal:2', 'due_date' => 'date', 'paid_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BillPayment::class)->latest();
    }
}
