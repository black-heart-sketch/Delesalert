<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(): View
    {
        return view('reports.create', ['zones' => Zone::orderBy('city')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['zone_id' => ['nullable', 'exists:zones,id'], 'description' => ['required', 'string', 'max:3000'], 'address' => ['nullable', 'string', 'max:255'], 'latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180']]);
        $request->user()->reports()->create($data + ['status' => 'PENDING', 'reported_at' => now()]);

        return redirect()->route('dashboard')->with('success', 'Thank you. Your report is awaiting review.');
    }
}
