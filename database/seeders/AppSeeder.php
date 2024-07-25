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
                'name' => 'Administrator Administrator Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.ADMIN_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.ADMIN_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend'
            ],
            [
                'name' => 'Wholesales Administrator Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => 5,
                'domain' => config('app.WHOLESALES_ADMIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.WHOLESALES_ADMIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend'
            ],
            [
                'name' => 'Supermarket Administrator Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => 6,
                'domain' => config('app.SUPERMARKET_ADMIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SUPERMARKET_ADMIN').config('app.PORT_POSTFIX'),
                'type' => 'Backend'
            ],
            [
                'name' => 'Sales Rep Administrator Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.SALES_REPRESENTATIVES'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SALES_REPRESENTATIVES').config('app.PORT_POSTFIX'),
                'type' => 'Backend'
            ],
            [
                'name' => 'Wholesales',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => 5,
                'domain' => config('app.WHOLESALES_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.WHOLESALES_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend'
            ],
            [
                'name' => 'SuperMarket',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => 6,
                'domain' => config('app.SUPERMARKET_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.SUPERMARKET_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend'
            ],
            [
                'name' => 'Authentication',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.AUTH_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.AUTH_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend'
            ],
            [
                'name' => 'Local Server Data Push',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.PUSH_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.PUSH_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend'
            ],
            [
                'name' => 'Images Application',
                'description' => NULL,
                'logo' => NULL,
                'model_id' => NULL,
                'domain' => config('app.IMAGES_DOMAIN'),
                'link' => config('app.HTTP_PROTOCOL'). config('app.IMAGES_DOMAIN').config('app.PORT_POSTFIX'),
                'type' => 'Frontend'
            ]
        ];

        DB::table("apps")->insert($applications);
    }
}
