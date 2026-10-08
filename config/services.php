<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'invoice_validation' => [
        'enabled' => env('INVOICE_VALIDATION_TUNNEL_ENABLED', true),
        'url' => env('INVOICE_VALIDATION_TUNNEL_URL', 'https://validation.generaldrugcentre.com/api/invoice/validate'),
        'api_key' => env('INVOICE_VALIDATION_TUNNEL_API_KEY'),
        'timeout' => (int) env('INVOICE_VALIDATION_TUNNEL_TIMEOUT', 8),
        'connect_timeout' => (int) env('INVOICE_VALIDATION_TUNNEL_CONNECT_TIMEOUT', 3),
    ],

];
