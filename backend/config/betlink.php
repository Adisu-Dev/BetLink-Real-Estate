<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BetLink Application Config
    |--------------------------------------------------------------------------
    */

    'name'         => env('APP_NAME', 'BetLink'),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),

    'property' => [
        'max_images'       => 20,
        'max_video_size'   => 50 * 1024, // 50MB in KB
        'max_image_size'   => 5 * 1024,  // 5MB in KB
        'approval_required'=> true,
        'expiry_days'      => 90,
        'featured_count'   => 12,
        'per_page'         => 15,
    ],

    'booking' => [
        'service_fee_percent' => 5,
        'min_nights'          => 1,
        'max_nights'          => 365,
    ],

    'subscription' => [
        'plans' => [
            'free'       => ['listings' => 3,   'featured' => 0,  'price' => 0],
            'basic'      => ['listings' => 10,  'featured' => 2,  'price' => 500],
            'premium'    => ['listings' => 50,  'featured' => 10, 'price' => 1500],
            'enterprise' => ['listings' => 999, 'featured' => 50, 'price' => 5000],
        ],
    ],

    'roles' => ['admin', 'owner', 'buyer', 'agent'],

    'currencies' => ['ETB', 'USD'],

    'listing_types' => ['sale', 'rent', 'short_rent'],
];
