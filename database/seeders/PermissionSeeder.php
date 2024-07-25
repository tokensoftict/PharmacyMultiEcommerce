<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'category.list',
            'category.create',
            'category.edit',
            'category.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdmin = Role::create(['name' => 'Super Administrator']);

        $superAdmin->syncPermissions([
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'category.list',
            'category.create',
            'category.edit',
            'category.delete',
        ]);

    }
}
