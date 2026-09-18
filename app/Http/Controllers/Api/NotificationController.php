<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $r)
    {
        return ['success' => true, 'data' => $r->user()->notifications()->latest()->paginate($r->integer('per_page', 15))];
    }

    public function read(Request $r, string $notification)
    {
        $item = $r->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        return ['success' => true, 'data' => $item];
    }

    public function preference(Request $r)
    {
        return ['success' => true, 'data' => NotificationPreference::firstOrCreate(['user_id' => $r->user()->id])];
    }

    public function updatePreference(Request $r)
    {
        $data = $r->validate(['push_enabled' => 'boolean', 'email_enabled' => 'boolean', 'sms_enabled' => 'boolean', 'scheduled_outage_alerts' => 'boolean', 'predicted_outage_alerts' => 'boolean', 'restoration_alerts' => 'boolean', 'minimum_risk_threshold' => 'in:LOW,MEDIUM,HIGH,CRITICAL']);
        $preference = NotificationPreference::updateOrCreate(['user_id' => $r->user()->id], $data);

        return ['success' => true, 'data' => $preference];
    }
}
