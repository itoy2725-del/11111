<?php

return [

    'name' => env('APP_NAME', 'Siber Güvenlik CRM'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'Europe/Istanbul',

    'locale' => 'tr',

    'fallback_locale' => 'en',

    'faker_locale' => 'tr_TR',

    'key' => env('APP_KEY', 'base64:c3VwZXJzZWNyZXRjcjJzaWJlcmd1dmVubGlrY3Jta2V5'),

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
