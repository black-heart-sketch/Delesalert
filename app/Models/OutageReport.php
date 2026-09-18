<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutageReport extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['reported_at' => 'datetime', 'validated_at' => 'datetime', 'latitude' => 'float', 'longitude' => 'float'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
