<?php

namespace Database\Seeders;

use App\Models\AppVersion;
use Illuminate\Database\Seeder;

class AppVersionSeeder extends Seeder
{
    /**
     * Seed initial Android and iOS version records.
     *
     * Run this once after migrating:
     *   php artisan db:seed --class=AppVersionSeeder
     *
     * After updating via the admin panel, the cache is automatically cleared.
     */
    public function run(): void
    {
        // Android
        AppVersion::updateOrCreate(
            ['app_type' => 'android', 'version_code' => 1],
            [
                'version_name'   => '1.0.0',
                'force_update'   => false,
                'update_message' => 'A new version of the PSGDC app is available! Update now for the best experience.',
                'store_url'      => env('ANDROID_STORE_URL', 'https://play.google.com/store/apps/details?id=com.psgdc'),
                'is_active'      => true,
            ]
        );

        // iOS
        AppVersion::updateOrCreate(
            ['app_type' => 'ios', 'version_code' => 1],
            [
                'version_name'   => '1.0.0',
                'force_update'   => false,
                'update_message' => 'A new version of the PSGDC app is available! Update now for the best experience.',
                'store_url'      => env('IOS_STORE_URL', 'https://apps.apple.com/app/id000000000'),
                'is_active'      => true,
            ]
        );
    }
}
