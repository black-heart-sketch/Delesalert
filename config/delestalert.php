<?php

return [
    'maps' => ['default_provider' => env('MAP_PROVIDER', 'leaflet'), 'google_maps_key' => env('GOOGLE_MAPS_API_KEY'), 'leaflet_tile_url' => env('LEAFLET_TILE_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')],
    'ai' => ['driver' => env('AI_DRIVER', 'openrouter'), 'openrouter_key' => env('OPENROUTER_API_KEY'), 'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'), 'url' => env('OPENROUTER_URL', 'https://openrouter.ai/api/v1/chat/completions')],
    'prediction' => ['medium_threshold' => (int) env('PREDICTION_MEDIUM_THRESHOLD', 40), 'high_threshold' => (int) env('PREDICTION_HIGH_THRESHOLD', 60), 'critical_threshold' => (int) env('PREDICTION_CRITICAL_THRESHOLD', 80)],
    'payments' => ['driver' => env('PAYMENT_DRIVER', 'demo')],
];
