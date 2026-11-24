<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const APP_IDS = [2, 3];

    private const PERMISSIONS = [
        [
            'name' => 'backend.admin.settings.opay_settings',
            'label' => 'OPay Settings',
            'visibility' => '1',
            'guard_name' => 'web',
        ],
        [
            'name' => 'backend.admin.settings.opay_settings.update',
            'label' => 'Update OPay Settings',
            'visibility' => '0',
            'guard_name' => 'web',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('modules') || ! Schema::hasTable('permissions')) {
            return;
        }

        $settingsModuleId = DB::table('modules')->where('name', 'Settings')->value('id');
        if (! $settingsModuleId) {
            return;
        }

        $hasAppsTable = Schema::hasTable('apps');
        $hasMapperTable = Schema::hasTable('permission_app_mappers');

        foreach (self::PERMISSIONS as $perm) {
            $existing = DB::table('permissions')
                ->where('name', $perm['name'])
                ->where('guard_name', $perm['guard_name'])
                ->first();

            if ($existing) {
                $permissionId = $existing->id;
                DB::table('permissions')
                    ->where('id', $permissionId)
                    ->update([
                        'module_id' => $settingsModuleId,
                        'label' => $perm['label'],
                        'visibility' => $perm['visibility'],
                        'updated_at' => now(),
                    ]);
            } else {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $perm['name'],
                    'module_id' => $settingsModuleId,
                    'label' => $perm['label'],
                    'visibility' => $perm['visibility'],
                    'guard_name' => $perm['guard_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($hasMapperTable && $hasAppsTable) {
                foreach (self::APP_IDS as $appId) {
                    if (! DB::table('apps')->where('id', $appId)->exists()) {
                        continue;
                    }

                    $mapped = DB::table('permission_app_mappers')
                        ->where('permission_id', $permissionId)
                        ->where('app_id', $appId)
                        ->exists();

                    if (! $mapped) {
                        DB::table('permission_app_mappers')->insert([
                            'permission_id' => $permissionId,
                            'app_id' => $appId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        foreach (self::APP_IDS as $appId) {
            Cache::forget('module-with-permission-list-' . $appId);
            Cache::forget('permission-list-' . $appId);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $names = array_column(self::PERMISSIONS, 'name');
        $permissionIds = DB::table('permissions')->whereIn('name', $names)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            if (Schema::hasTable('permission_app_mappers')) {
                DB::table('permission_app_mappers')->whereIn('permission_id', $permissionIds)->delete();
            }
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }

        foreach (self::APP_IDS as $appId) {
            Cache::forget('module-with-permission-list-' . $appId);
            Cache::forget('permission-list-' . $appId);
        }
    }
};
