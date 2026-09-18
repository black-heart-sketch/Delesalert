<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outage;
use App\Services\NotifyAffectedClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OutageController extends Controller
{
    public function current(Request $r)
    {
        return $this->list($r, Outage::where('status', 'ONGOING'));
    }

    public function scheduled(Request $r)
    {
        return $this->list($r, Outage::where('status', 'PLANNED'));
    }

    public function history(Request $r)
    {
        return $this->list($r, Outage::whereIn('status', ['RESOLVED', 'CANCELLED']));
    }

    public function show(Outage $outage)
    {
        return ['success' => true, 'data' => $outage];
    }

    public function store(Request $r, NotifyAffectedClients $notifyAffectedClients)
    {
        Gate::authorize('create', Outage::class);
        $data = $this->data($r);
        $data['source'] = $r->user()->isAdmin() ? ($data['source'] ?? 'ADMIN') : 'PROVIDER';
        $outage = Outage::create($this->withLifecycleTimestamps($data) + ['created_by' => $r->user()->id]);
        $notifyAffectedClients->handle($outage);

        return response()->json(['success' => true, 'data' => $outage], 201);
    }

    public function update(Request $r, Outage $outage, NotifyAffectedClients $notifyAffectedClients)
    {
        Gate::authorize('update', $outage);
        $previousStatus = $outage->status;
        $data = $this->data($r, true);

        if (! $r->user()->isAdmin()) {
            unset($data['source']);
        }

        $outage->update($this->withLifecycleTimestamps($data, $previousStatus));

        if (isset($data['status']) && $data['status'] !== $previousStatus) {
            $notifyAffectedClients->handle($outage->fresh());
        }

        return ['success' => true, 'data' => $outage->fresh()];
    }

    private function list(Request $r, $query)
    {
        return ['success' => true, 'data' => $query->latest()->paginate($r->integer('per_page', 15))];
    }

    private function data(Request $r, bool $partial = false): array
    {
        return $r->validate(['zone_id' => [$partial ? 'sometimes' : 'required', 'exists:zones,id'], 'title' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'], 'description' => 'nullable|string', 'type' => [$partial ? 'sometimes' : 'required', 'in:SCHEDULED,UNPLANNED,PREDICTED'], 'status' => [$partial ? 'sometimes' : 'required', 'in:PLANNED,ONGOING,RESOLVED,CANCELLED'], 'source' => ['sometimes', 'in:PROVIDER,USER_REPORT,AI,ADMIN'], 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180', 'affected_radius_km' => 'nullable|numeric|min:0', 'scheduled_start' => 'nullable|date', 'expected_end' => 'nullable|date', 'actual_start' => 'nullable|date', 'actual_end' => 'nullable|date', 'estimated_duration_minutes' => 'nullable|integer|min:1']);
    }

    private function withLifecycleTimestamps(array $data, ?string $previousStatus = null): array
    {
        if (($data['status'] ?? null) === 'ONGOING' && $previousStatus !== 'ONGOING' && empty($data['actual_start'])) {
            $data['actual_start'] = now();
        }

        if (($data['status'] ?? null) === 'RESOLVED' && $previousStatus !== 'RESOLVED' && empty($data['actual_end'])) {
            $data['actual_end'] = now();
        }

        return $data;
    }
}
