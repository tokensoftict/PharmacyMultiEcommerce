<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'name' => 'Administrator Administrator',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.ADMIN_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.ADMIN_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend',
                'show' => false,
            ],
            [
                'name' => '📦 Wholesales Administrator',
                'description' => 'Oversee products, bulk orders and operations',
                'logo' => NULL,
                'model_id' => 5,
                'domain' => config('app.WHOLESALES_ADMIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.WHOLESALES_ADMIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend',
                'show' => true,
            ],
            [
                'name' => '🛒 Supermarket Administrator',
                'description' => 'Manage products, orders, and staff in Store',
                'logo' => NULL,
                'model_id' => 6,
                'domain' => config('app.SUPERMARKET_ADMIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SUPERMARKET_ADMIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend',
                'show' => true,
            ],
            [
                'name' => '💼 Sales Rep Administrator',
                'description' => 'Refer customers, grow your network, and earn rewards.',
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.SALES_REPRESENTATIVES'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SALES_REPRESENTATIVES').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => false,
            ],
            [
                'name' => 'Wholesales',
                'description' => 'Buy medications and health supplies in bulk.',
                'logo' => NULL,
                'model_id' => 5,
                'domain' => config('app.WHOLESALES_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.WHOLESALES_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => true,
            ],
            [
                'name' => 'SuperMarket',
                'description' => 'Shop for groceries, household essentials, medicines, personal care items, and much more—all in one convenient place.',
                'logo' => NULL,
                'model_id' => 6,
                'domain' => config('app.SUPERMARKET_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SUPERMARKET_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => true,
            ],
            [
                'name' => 'Authentication',
                'description' => '',
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.AUTH_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.AUTH_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => false,
            ],
            [
                'name' => 'Local Server Data Push',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.PUSH_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.PUSH_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => false,
            ],
            [
                'name' => 'Images Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.IMAGES_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.IMAGES_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend',
                'show' => false,
            ]
        ];

        DB::table("apps")->insert($applications);
    }
}
