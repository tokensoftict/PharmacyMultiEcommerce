<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class SuperAdminstratorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('app:populate-permissions');

        Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('app.SUPER_ADMINISTRATOR')],
            ['guard_name' => 'web', 'name' => config('app.SUPER_ADMINISTRATOR')]
        );

    }
}
