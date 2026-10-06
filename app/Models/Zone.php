<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Zone extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float'];
    }

    public function outages(): HasMany
    {
        return $this->hasMany(Outage::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(OutageReport::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function latestPrediction(): HasOne
    {
        return $this->predictions()->one()->latestOfMany();
    }
}
