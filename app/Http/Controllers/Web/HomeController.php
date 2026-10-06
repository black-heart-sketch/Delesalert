<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ZoneMapData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ZoneMapData $zoneMapData): View
    {
        return view('welcome', [
            'mapPayload' => $zoneMapData->build(),
            'mapTileUrl' => config('delestalert.maps.leaflet_tile_url'),
        ]);
    }
}
