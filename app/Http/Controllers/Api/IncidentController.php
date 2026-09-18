<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index(Request $r)
    {
        return ['success' => true, 'data' => Incident::where('provider_id', $r->user()->id)->latest()->paginate($r->integer('per_page', 15))];
    }

    public function store(Request $r)
    {
        $data = $this->data($r);

        return response()->json(['success' => true, 'data' => Incident::create($data + ['provider_id' => $r->user()->id])], 201);
    }

    public function show(Request $r, Incident $incident)
    {
        abort_unless($incident->provider_id === $r->user()->id || $r->user()->isAdmin(), 403);

        return ['success' => true, 'data' => $incident];
    }

    public function update(Request $r, Incident $incident)
    {
        abort_unless($incident->provider_id === $r->user()->id || $r->user()->isAdmin(), 403);
        $incident->update($this->data($r, true));

        return ['success' => true, 'data' => $incident];
    }

    private function data(Request $r, bool $p = false): array
    {
        return $r->validate(['zone_id' => [$p ? 'sometimes' : 'required', 'exists:zones,id'], 'title' => [$p ? 'sometimes' : 'required', 'string'], 'description' => [$p ? 'sometimes' : 'required', 'string'], 'incident_type' => [$p ? 'sometimes' : 'required', 'string'], 'severity' => [$p ? 'sometimes' : 'required', 'in:LOW,MEDIUM,HIGH,CRITICAL'], 'status' => 'sometimes|in:OPEN,IN_PROGRESS,RESOLVED', 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180', 'occurred_at' => [$p ? 'sometimes' : 'required', 'date']]);
    }
}
