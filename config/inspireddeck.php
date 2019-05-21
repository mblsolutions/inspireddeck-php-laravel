<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inspired Deck API Endpoint
    |--------------------------------------------------------------------------
    |
    | The Inspired Deck API endpoint
    |
    */

    'endpoint' => env('ID_ENDPOINT', 'https://inspireddeck.co.uk'),

    /*
    |--------------------------------------------------------------------------
    | Inspired Deck OAuth Client ID
    |--------------------------------------------------------------------------
    |
    | The OAuth Client ID for the application, the ID for your application
    | will be supplied by MBL Solutions.
    |
    */

    'client_id' => env('ID_CLIENT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Inspired Deck OAuth Secret
    |--------------------------------------------------------------------------
    |
    | The OAuth secret for the application, the secret for your application
    | will be supplied by MBL Solutions.
    |
    */

    'secret' => env('ID_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you can specify the session cookie name used to identify the an
    | Inspired Deck Authentication session instance.
    |
    */

    'session' => env('ID_SESSION', 'inspireddeck_auth_session'),

    /*
    |--------------------------------------------------------------------------
    | Inspired Deck Roles
    |--------------------------------------------------------------------------
    |
    | The Inspired Deck Roles that are allowed access to the application
    |
    */

    'roles' => [
        'admin',
        'programme_manager',
        'customer_service_manager',
        'customer_service_operator',
        'store_manager',
        'store_operator',
        //'report',
    ]

];