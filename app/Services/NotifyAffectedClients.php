<?php

namespace App\Services;

use App\Models\Outage;
use App\Models\User;
use App\Notifications\OutageStatusNotification;

class NotifyAffectedClients
{
    public function handle(Outage $outage): void
    {
        if (! in_array($outage->status, ['PLANNED', 'ONGOING', 'RESOLVED'], true)) {
            return;
        }

        User::query()
            ->where('role', 'CLIENT')
            ->where('status', 'ACTIVE')
            ->whereHas('locations', fn ($query) => $query->where('zone_id', $outage->zone_id))
            ->with('notificationPreference')
            ->eachById(function (User $client) use ($outage): void {
                $preference = $client->notificationPreference;

                if ($outage->status === 'PLANNED' && $preference && ! $preference->scheduled_outage_alerts) {
                    return;
                }

                if ($outage->status === 'RESOLVED' && $preference && ! $preference->restoration_alerts) {
                    return;
                }

                $client->notify(new OutageStatusNotification($outage));
            });
    }
}
