<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prediction;
use App\Models\Zone;
use App\Services\PredictionService;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function index(Request $r)
    {
        return ['success' => true, 'data' => Prediction::with('zone')->latest('generated_at')->paginate($r->integer('per_page', 15))];
    }

    public function forZone(Zone $zone)
    {
        return ['success' => true, 'data' => Prediction::where('zone_id', $zone->id)->latest('generated_at')->first()];
    }

    public function generate(Request $r, PredictionService $service)
    {
        $data = $r->validate(['zone_id' => 'required|exists:zones,id']);

        return response()->json(['success' => true, 'data' => $service->generate(Zone::findOrFail($data['zone_id']))], 201);
    }
}
