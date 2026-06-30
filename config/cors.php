<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://chatmego.com', 'https://m.chatmego.com', 'http://localhost:8888', 'http://127.0.0.1:8888'],

    'allowed_origins_patterns' => ['/^https?:\/\/.*\.chatmego\.com$/'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
