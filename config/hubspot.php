<?php 

return [
    'auth_middleware' => env('HUBSPOT_AUTH_MIDDLEWARE', 'auth:web'),
    'app_id' => env('HUBSPOT_APP_ID'),
    'client_id' => env('HUBSPOT_CLIENT_ID'),
    'client_secret' => env('HUBSPOT_CLIENT_SECRET')
];