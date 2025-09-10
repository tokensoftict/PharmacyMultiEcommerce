<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'SUPER_ADMINISTRATOR' => env('SUPER_ADMINISTRATOR', 'Super Administrator'),

    /** Admin URL */
    'WHOLESALES_ADMIN' => env('WHOLESALES_ADMIN', 'wa.psgdc.store'),
    'WHOLESALES_ADMIN_ROUTE_PREFIX' => env('WHOLESALES_ADMIN_ROUTE_PREFIX', 'wa.'),

    'SUPERMARKET_ADMIN' => env('SUPERMARKET_ADMIN', 'sa.psgdc.store'),
    'SUPERMARKET_ADMIN_ROUTE_PREFIX' => env('SUPERMARKET_ADMIN_ROUTE_PREFIX', 'sa.'),

    'WHOLESALES_DOMAIN' => env('WHOLESALES_DOMAIN', 'wholesales.psgdc.store'),
    'WHOLESALES_DOMAIN_ROUTE_PREFIX' => env('WHOLESALES_DOMAIN_ROUTE_PREFIX', 'wholesales.'),

    'SUPERMARKET_DOMAIN' => env('SUPERMARKET_DOMAIN', 'supermarket.psgdc.store'),
    'SUPERMARKET_DOMAIN_ROUTE_PREFIX' => env('SUPERMARKET_DOMAIN_ROUTE_PREFIX', 'supermarket.'),



    /** Main URL */
    'MAIN_DOMAIN' => env('MAIN_DOMAIN', 'psgdc.store'),
    'AUTH_DOMAIN' => env('AUTH_DOMAIN', "auth.psgdc.store"),
    'ADMIN_DOMAIN' => env('ADMIN_DOMAIN', 'admin.psgdc.store'),
    "PUSH_DOMAIN" => env("PUSH_DOMAIN", "pa.psgdc.store"),
    'SALES_REPRESENTATIVES' => env('SALES_REPRESENTATIVES', 'rep.psgdc.store'),
    'SALES_REPRESENTATIVES_ROUTE_PREFIX' => env('SALES_REPRESENTATIVES_ROUTE_PREFIX', 'sales_representatives.'),
    /** Api URL */


    /** Auth URL */


    /** Front End URL */





    /** Sales Rep End URL */

    'API_DOMAIN' => env('API_DOMAIN', 'api.psgdc.store'),


    'DEFAULT_COUNTRY_ID' => env('DEFAULT_COUNTRY_ID', 160),
    'IMAGES_DOMAIN'      => env('IMAGES_DOMAIN', 'cdn.psgdc.store'),
    'PORT_POSTFIX' => env("PORT_POSTFIX", ""),
    'HTTP_PROTOCOL' => env("HTTP_PROTOCOL", "https://"),
    'CRM_DATA_CACHE_TTL' => env("CRM_DATA_CACHE_TTL", 86400),
    'PAGINATE_NUMBER' => env("PAGINATE_NUMBER", 15),
    'KAFKA_HEADER_KEY' => env("KAFKA_HEADER_KEY", "PSGDC"),


    /** SMS NOTIFICATION SETTINGS */
    'BULKSMS_ENGINE' => env("BULKSMS_ENGINE", "OTHERS"),
    'BULKSMS_EMAIL' => env("BULKSMS_EMAIL"),
    'BULKSMS_PASSWORD' => env("BULKSMS_PASSWORD"),
    'BULKSMS_URL' => env("BULKSMS_URL"),
    'BULKSMS_SENDER' => env("BULKSMS_SENDER"),

    /**  SEND CHAMP DETAILS */
    'SEND_CHAMP_AUTHORIZATION' => env("SEND_CHAMP_AUTHORIZATION", ""),
    'SEND_CHAMP_TOKEN_LENGTH' => env("SEND_CHAMP_TOKEN_LENGTH", 4),
    'SEND_CHAMP_EXPIRATION_TIME' => env("SEND_CHAMP_EXPIRATION_TIME", 5),

    'PROCESS_OLD_APP_URL' => env("PROCESS_OLD_APP_URL", "https://admin.generaldrugcentre.com/api/data/"),
    'mustVerify' => env("MUST_VERIFY", 'email'),
];
