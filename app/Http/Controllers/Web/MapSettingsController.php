<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\MapConfiguration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapSettingsController extends Controller
{
    public function edit(MapConfiguration $mapConfiguration): View
    {
        return view('admin.map-settings', ['map' => $mapConfiguration->current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['provider' => ['required', 'in:google,leaflet']]);
        SystemSetting::updateOrCreate(['key' => 'map_provider'], ['value' => $data['provider']]);

        return redirect()->route('admin.map-settings.edit')->with('success', 'Map provider updated.');
    }
}
