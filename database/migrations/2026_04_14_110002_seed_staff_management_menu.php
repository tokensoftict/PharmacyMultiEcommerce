<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert module
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'staff_management',
            'label' => 'Staff Management',
            'status' => 1,
            'visibility' => 1,
            'order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert permission
        $permutationId = DB::table('permissions')->insertGetId([
            'name' => 'backend.admin.staff.list',
            'module_id' => $moduleId,
            'label' => 'Staff List',
            'visibility' => 1,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Associate with apps (Supermarket=2, Wholesales=3)
        foreach ([2, 3] as $appId) {
            DB::table('module_app_mappers')->insert([
                'module_id' => $moduleId,
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('permission_app_mappers')->insert([
                'permission_id' => $permutationId,
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('module-with-permission-list-2');
        \Illuminate\Support\Facades\Cache::forget('module-with-permission-list-3');
    }

    public function down(): void
    {
        $moduleId = DB::table('modules')->where('name', 'staff_management')->value('id');
        if ($moduleId) {
            DB::table('permissions')->where('module_id', $moduleId)->delete();
            DB::table('module_app_mappers')->where('module_id', $moduleId)->delete();
            DB::table('modules')->where('id', $moduleId)->delete();
        }
    }
};
