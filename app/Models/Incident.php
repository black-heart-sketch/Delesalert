<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime', 'latitude' => 'float', 'longitude' => 'float'];
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
