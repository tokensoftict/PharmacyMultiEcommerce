<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OPay Offline API Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration values for communicating with OPay's Offline API
    | (POS & Others) using RSA encryption and SHA256withRSA request signing.
    | Database-stored values in `opay_settings` take precedence over these
    | environment fallbacks when present.
    |
    | Reference: https://documentation.opayweb.com/doc/offline/authentication.html
    |
    */

    'enabled' => (bool) env('OPAY_ENABLED', false),

    'environment' => env('OPAY_ENVIRONMENT', 'live'),

    'base_url' => rtrim((string) env('OPAY_BASE_URL', 'https://payapi.opayweb.com'), '/'),

    'api_version' => env('OPAY_API_VERSION', 'V1.0.1'),

    'body_format' => 'JSON',

    'timeout' => (int) env('OPAY_HTTP_TIMEOUT', 30),

    'connect_timeout' => (int) env('OPAY_HTTP_CONNECT_TIMEOUT', 10),

    'key_bits' => (int) env('OPAY_RSA_KEY_BITS', 1024),

    'head_merchant_id' => env('OPAY_HEAD_MERCHANT_ID'),

    'merchant_id' => env('OPAY_MERCHANT_ID'),

    'client_auth_key' => env('OPAY_CLIENT_AUTH_KEY'),

    'opay_public_key' => env('OPAY_PUBLIC_KEY'),

    'merchant_public_key' => env('OPAY_MERCHANT_PUBLIC_KEY'),

    'merchant_private_key' => env('OPAY_MERCHANT_PRIVATE_KEY'),

    'ip_whitelisting' => env('OPAY_IP_WHITELISTING'),

    'webhook_url' => env('OPAY_WEBHOOK_URL'),

    'kafka' => [
        'enabled' => (bool) env('OPAY_KAFKA_FORWARD_ENABLED', true),
        'topic' => \App\Enums\KafkaTopics::OPAY_KAFKA_TOPIC,
    ],
];
