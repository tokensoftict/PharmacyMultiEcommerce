<?php

namespace Database\Seeders;

use App\Models\DeliveryWithinIlorin;
use Illuminate\Database\Seeder;

class DeliveryWithinIlorinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryWithinIlorin::truncate();

        $deliveryMethods = \App\Models\DeliveryMethod::whereIn('code', ['Dwi', 'DI-ILN'])->get();

        foreach ($deliveryMethods as $method) {
            if ($method->template_settings_value && is_array($method->template_settings_value)) {
                foreach ($method->template_settings_value as $setting) {
                    DeliveryWithinIlorin::create([
                        'app_id' => $method->app_id,
                        'name' => $setting['name'] ?? $setting['title'] ?? 'Unknown',
                        'amount' => $setting['amount'] ?? 0,
                        'status' => true,
                    ]);
                }
            }
        }
    }
}
