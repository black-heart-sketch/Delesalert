<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outage extends Model
{
    protected $guarded = [];

    protected $with = ['zone'];

    protected function casts(): array
    {
        return ['scheduled_start' => 'datetime', 'expected_end' => 'datetime', 'actual_start' => 'datetime', 'actual_end' => 'datetime', 'latitude' => 'float', 'longitude' => 'float', 'affected_radius_km' => 'float'];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
