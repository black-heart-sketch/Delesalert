<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function mine(Request $r)
    {
        return ['success' => true, 'data' => $r->user()->reports()->latest()->paginate($r->integer('per_page', 15))];
    }

    public function store(Request $r)
    {
        $data = $r->validate(['zone_id' => 'nullable|exists:zones,id', 'description' => 'required|string|max:3000', 'address' => 'nullable|string', 'latitude' => 'required|numeric|between:-90,90', 'longitude' => 'required|numeric|between:-180,180', 'reported_at' => 'nullable|date']);
        $report = $r->user()->reports()->create($data + ['status' => 'PENDING', 'reported_at' => $data['reported_at'] ?? now()]);

        return response()->json(['success' => true, 'data' => $report], 201);
    }
}
