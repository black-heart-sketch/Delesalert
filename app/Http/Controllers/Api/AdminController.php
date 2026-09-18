<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutageReport;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Zone;
use App\Services\MapConfiguration;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(Request $r)
    {
        return ['success' => true, 'data' => User::when($r->q, fn ($q, $v) => $q->where('name', 'like', "%$v%"))->paginate($r->integer('per_page', 15))];
    }

    public function userStatus(Request $r, User $user)
    {
        $data = $r->validate(['status' => 'required|in:ACTIVE,SUSPENDED,DEACTIVATED']);
        $user->update($data);

        return ['success' => true, 'data' => $user];
    }

    public function zones()
    {
        return ['success' => true, 'data' => Zone::orderBy('region')->paginate(30)];
    }

    public function storeZone(Request $r)
    {
        return response()->json(['success' => true, 'data' => Zone::create($this->zoneData($r))], 201);
    }

    public function updateZone(Request $r, Zone $zone)
    {
        $zone->update($this->zoneData($r, true));

        return ['success' => true, 'data' => $zone];
    }

    public function deleteZone(Zone $zone)
    {
        $zone->delete();

        return response()->noContent();
    }

    public function reports(Request $r)
    {
        return ['success' => true, 'data' => OutageReport::with('user', 'zone')->latest()->paginate($r->integer('per_page', 15))];
    }

    public function reviewReport(Request $r, OutageReport $report)
    {
        $d = $r->validate(['status' => 'required|in:VALIDATED,REJECTED', 'rejection_reason' => 'required_if:status,REJECTED|nullable|string']);
        $report->update($d + ['validated_by' => $r->user()->id, 'validated_at' => now()]);

        return ['success' => true, 'data' => $report];
    }

    public function mapConfiguration(MapConfiguration $maps)
    {
        return ['success' => true, 'data' => $maps->current()];
    }

    public function updateMapConfiguration(Request $r, MapConfiguration $maps)
    {
        $data = $r->validate(['provider' => 'required|in:google,leaflet']);
        SystemSetting::updateOrCreate(['key' => 'map_provider'], ['value' => $data['provider']]);

        return ['success' => true, 'data' => $maps->current()];
    }

    private function zoneData(Request $r, bool $p = false): array
    {
        return $r->validate(['name' => [$p ? 'sometimes' : 'required', 'string'], 'region' => [$p ? 'sometimes' : 'required', 'string'], 'city' => [$p ? 'sometimes' : 'required', 'string'], 'district' => 'nullable|string', 'latitude' => [$p ? 'sometimes' : 'required', 'numeric'], 'longitude' => [$p ? 'sometimes' : 'required', 'numeric']]);
    }
}
