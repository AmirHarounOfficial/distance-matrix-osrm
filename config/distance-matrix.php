<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Distance Matrix Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "osrm" (Future: "google", "mapbox")
    |
    */
    'driver' => env('DISTANCE_MATRIX_DRIVER', 'osrm'),

    /*
    |--------------------------------------------------------------------------
    | OSRM Configuration
    |--------------------------------------------------------------------------
    */
    'osrm' => [
        'base_url' => env('OSRM_BASE_URL', 'http://router.project-osrm.org'),
        'profile' => 'driving', // driving, car, bike, foot
        'timeout' => 10,
        'retry' => [
            'enabled' => true,
            'times' => 3,
            'sleepMs' => 1000,
        ],
        'cache' => [
            'enabled' => false,
            'ttl' => 3600,
        ],
    ],
];
