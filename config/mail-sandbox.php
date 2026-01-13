<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mail Sandbox Capture
    |--------------------------------------------------------------------------
    |
    | This setting determines if the package should listen to mail events.
    | By default, it is enabled in non-production environments.
    |
    */
    'capture_enabled' => env('MAIL_SANDBOX_CAPTURE', config('app.env') !== 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mail Sandbox UI
    |--------------------------------------------------------------------------
    |
    | This setting determines if the web UI is available.
    | By default, it is enabled in non-production environments.
    |
    */
    'ui_enabled' => env('MAIL_SANDBOX_UI', config('app.env') !== 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mail Sandbox Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Mail Sandbox will be accessible.
    |
    */
    'path' => 'mail-sandbox',

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware attached to Mail Sandbox routes.
    |
    */
    'middleware' => ['web'],
];
