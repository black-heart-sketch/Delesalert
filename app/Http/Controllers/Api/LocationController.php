<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedLocation;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $r)
    {
        return ['success' => true, 'data' => $r->user()->locations()->get()];
    }

    public function store(Request $r)
    {
        $data = $this->data($r);
        if ($data['is_primary'] ?? false) {
            $r->user()->locations()->update(['is_primary' => false]);
        }

        return response()->json(['success' => true, 'data' => $r->user()->locations()->create($data)], 201);
    }

    public function update(Request $r, SavedLocation $location)
    {
        abort_unless($location->user_id === $r->user()->id, 403);
        $data = $this->data($r, true);
        if ($data['is_primary'] ?? false) {
            $r->user()->locations()->whereKeyNot($location)->update(['is_primary' => false]);
        } $location->update($data);

        return ['success' => true, 'data' => $location];
    }

    public function destroy(Request $r, SavedLocation $location)
    {
        abort_unless($location->user_id === $r->user()->id, 403);
        $location->delete();

        return response()->noContent();
    }

    private function data(Request $r, bool $p = false): array
    {
        return $r->validate(['label' => [$p ? 'sometimes' : 'required', 'string'], 'address' => [$p ? 'sometimes' : 'required', 'string'], 'zone_id' => 'nullable|exists:zones,id', 'city' => 'nullable|string', 'district' => 'nullable|string', 'region' => 'nullable|string', 'latitude' => [$p ? 'sometimes' : 'required', 'numeric', 'between:-90,90'], 'longitude' => [$p ? 'sometimes' : 'required', 'numeric', 'between:-180,180'], 'is_primary' => 'boolean']);
    }
}
