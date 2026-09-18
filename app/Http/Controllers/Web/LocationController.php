<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SavedLocation;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        return view('locations.index', ['locations' => $request->user()->locations()->with('zone')->latest()->get(), 'zones' => Zone::orderBy('city')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['label' => ['required', 'string', 'max:100'], 'address' => ['required', 'string', 'max:255'], 'zone_id' => ['nullable', 'exists:zones,id'], 'latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180'], 'is_primary' => ['nullable', 'boolean']]);
        if ($request->boolean('is_primary')) {
            $request->user()->locations()->update(['is_primary' => false]);
        }
        $request->user()->locations()->create($data);

        return back()->with('success', 'Location saved successfully.');
    }

    public function destroy(Request $request, SavedLocation $location): RedirectResponse
    {
        abort_unless($location->user_id === $request->user()->id, 403);
        $location->delete();

        return back()->with('success', 'Location removed.');
    }
}
