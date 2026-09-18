<?php

namespace App\Services;

use App\Models\SystemSetting;

class MapConfiguration
{
    public function current(): array
    {
        $provider = SystemSetting::value('map_provider', config('delestalert.maps.default_provider'));

        return ['provider' => $provider, 'googleMapsKey' => $provider === 'google' ? config('delestalert.maps.google_maps_key') : null, 'leafletTileUrl' => $provider === 'leaflet' ? config('delestalert.maps.leaflet_tile_url') : null];
    }
}
