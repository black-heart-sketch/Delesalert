<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['push_enabled' => 'boolean', 'email_enabled' => 'boolean', 'sms_enabled' => 'boolean', 'scheduled_outage_alerts' => 'boolean', 'predicted_outage_alerts' => 'boolean', 'restoration_alerts' => 'boolean'];
    }
}
