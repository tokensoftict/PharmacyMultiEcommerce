<?php

/**
 * App Update Fallback Config
 * ─────────────────────────────────────────────────────────────────────────────
 * These values are ONLY used when the database is unavailable.
 * In normal operation, version info is read from the `app_versions` table.
 *
 * Add the following keys to your .env to use this fallback:
 *
 *   ANDROID_LATEST_VERSION=1.0.0
 *   ANDROID_LATEST_VERSION_CODE=1
 *   ANDROID_FORCE_UPDATE=false
 *   ANDROID_STORE_URL=https://play.google.com/store/apps/details?id=com.yourapp
 *
 *   IOS_LATEST_VERSION=1.0.0
 *   IOS_LATEST_VERSION_CODE=1
 *   IOS_FORCE_UPDATE=false
 *   IOS_STORE_URL=https://apps.apple.com/app/id000000000
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (seconds)
    |--------------------------------------------------------------------------
    | How long to cache version lookups. Default: 5 minutes (300 seconds).
    | Increase this in production for performance; clear cache after updates.
    */
    'cache_ttl' => env('APP_UPDATE_CACHE_TTL', 300),

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    */
    'cache_key' => 'app_version',

    /*
    |--------------------------------------------------------------------------
    | Fallback — Android
    |--------------------------------------------------------------------------
    */
    'android' => [
        'latest_version'      => env('ANDROID_LATEST_VERSION', '1.0.0'),
        'latest_version_code' => (int) env('ANDROID_LATEST_VERSION_CODE', 1),
        'force_update'        => (bool) env('ANDROID_FORCE_UPDATE', false),
        'store_url'           => env('ANDROID_STORE_URL', ''),
        'update_message'      => 'A new version of the app is available. Please update.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback — iOS
    |--------------------------------------------------------------------------
    */
    'ios' => [
        'latest_version'      => env('IOS_LATEST_VERSION', '1.0.0'),
        'latest_version_code' => (int) env('IOS_LATEST_VERSION_CODE', 1),
        'force_update'        => (bool) env('IOS_FORCE_UPDATE', false),
        'store_url'           => env('IOS_STORE_URL', ''),
        'update_message'      => 'A new version of the app is available. Please update.',
    ],

];
