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
    'WHOLESALES_ADMIN' => env('WHOLESALES_ADMIN', 'wholesales.admin.generaldrugcentre.com'),
    'SUPERMARKET_ADMIN' => env('SUPERMARKET_ADMIN', 'supermarket.admin.generaldrugcentre.com'),

    'SUPERMARKET_ADMIN_ROUTE_PREFIX' => env('SUPERMARKET_ADMIN_ROUTE_PREFIX', 'supermarket.admin.'),
    'WHOLESALES_ADMIN_ROUTE_PREFIX' => env('WHOLESALES_ADMIN_ROUTE_PREFIX', 'wholesales.admin.'),
    'WHOLESALES_DOMAIN_ROUTE_PREFIX' => env('WHOLESALES_DOMAIN_ROUTE_PREFIX', 'wholesales.'),
    'SUPERMARKET_DOMAIN_ROUTE_PREFIX' => env('SUPERMARKET_DOMAIN_ROUTE_PREFIX', 'supermarket.'),
    'SALES_REPRESENTATIVES_ROUTE_PREFIX' => env('SALES_REPRESENTATIVES_ROUTE_PREFIX', 'sales_representatives.'),


    'ADMIN_DOMAIN' => env('ADMIN_DOMAIN', 'admin.generaldrugcentre.com'),

    /** Main URL */
    'MAIN_DOMAIN' => env('MAIN_DOMAIN', 'generaldrugcentre.com'),

    /** Api URL */
    'API_DOMAIN' => env('API_DOMAIN', 'api.generaldrugcentre.com'),

    /** Auth URL */
    'AUTH_DOMAIN' => env('AUTH_DOMAIN', 'auth.generaldrugcentre.com'),

    /** Front End URL */
    'WHOLESALES_DOMAIN' => env('WHOLESALES_DOMAIN', 'wholesales.generaldrugcentre.com'),
    'SUPERMARKET_DOMAIN' => env('SUPERMARKET_DOMAIN', 'supermarket.generaldrugcentre.com'),
    "PUSH_DOMAIN" => env("PUSH_DOMAIN", "push.admin.generaldrugcentre.com"),


    /** Sales Rep End URL */
    'SALES_REPRESENTATIVES' => env('SALES_REPRESENTATIVES', 'rep.generaldrugcentre.com'),


    'DEFAULT_COUNTRY_ID' => env('DEFAULT_COUNTRY_ID', 160),
    'IMAGES_DOMAIN'      => env('IMAGES_DOMAIN', 'cdn.generaldrugcentre.com'),
    'PORT_POSTFIX' => env("PORT_POSTFIX", ""),
    'HTTP_PROTOCOL' => env("HTTP_PROTOCOL", "https://"),
    'BULKSMS_EMAIL' => env("BULKSMS_EMAIL", "generaldrugcentre@gmail.com"),
    'BULKSMS_PASSWORD' => env("BULKSMS_PASSWORD", "qazwsxedc"),
    'BULKSMS_URL' => env("BULKSMS_URL", "https://account.bulk-sms.ng/api/transactional/v2/send"),
    'BULKSMS_SENDER' => env("BULKSMS_SENDER", "PS GDC"),
    'CRM_DATA_CACHE_TTL' => env("CRM_DATA_CACHE_TTL", 86400),
    'PAGINATE_NUMBER' => env("PAGINATE_NUMBER", 15),
    'KAFKA_HEADER_KEY' => env("KAFKA_HEADER_KEY", "PSGDC"),
];
