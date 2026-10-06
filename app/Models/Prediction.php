<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['predicted_start' => 'datetime', 'predicted_end' => 'datetime', 'generated_at' => 'datetime', 'probability' => 'float', 'confidence_score' => 'float', 'input_metrics' => 'array', 'factors' => 'array', 'ai_generated' => 'boolean'];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
