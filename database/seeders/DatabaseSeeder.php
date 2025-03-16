<?php

namespace Database\Seeders;

use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaultSettingsSeeder::class,
            BanksSeeder::class,
            AppSeeder::class,
            CountriesTableSeeder::class,
            StateTableSeeder::class,
            LocalgovtSeeder::class,
            PopulateTownCitiesSeeder::class,
            SuperAdminstratorRoleSeeder::class,
            UserSeeder::class,
            DeliveryMethodSeeder::class,
            PaymentMethodSeeder::class,
            StatusSeeder::class,
            //MeilisearchSeeder::class
        ]);

    }
}
