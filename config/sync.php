<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Client Mode
    |--------------------------------------------------------------------------
    |
    | The same codebase runs as the central server (solforbs.com) and as the
    | offline client packaged by NativePHP. This switch is what tells them
    | apart, and it defaults to false so the server is never accidentally
    | the thing being switched.
    |
    | When true, writes to synced models are recorded to a local mutation
    | log for pushing later. When false nothing is logged, and the server
    | behaves exactly as it did before any of this existed.
    |
    */
    'client_mode' => (bool) env('TETHER_CLIENT_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Device Credentials
    |--------------------------------------------------------------------------
    |
    | Issued by Dashboard > Sync Devices when the device is enrolled. The
    | token is a Sanctum token limited to the 'sync' ability; the client id
    | identifies this particular device to the server.
    |
    | Only ever set on a client. On the server these stay empty.
    |
    */
    'server_url' => env('TETHER_SERVER_URL', ''),
    'device_token' => env('TETHER_DEVICE_TOKEN', ''),
];
