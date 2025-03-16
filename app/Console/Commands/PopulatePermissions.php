<?php

namespace App\Console\Commands;

use App\Enums\Permission\CouponManager;
use App\Enums\Permission\CustomerManager;
use App\Enums\Permission\MedReminder;
use App\Enums\Permission\OrderManager;
use App\Enums\Permission\PromotionalManager;
use App\Enums\Permission\PushNotification;
use App\Enums\Permission\SalesRepManager;
use App\Enums\Permission\Settings;
use App\Enums\Permission\StockManager;
use App\Enums\Permission\VoucherManager;
use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PopulatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $permissionAppIDS = [2,3];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        $model_has_permissions = DB::table('model_has_permissions')->get()->map(function($item){
            return (array)$item;
        })->toArray();

        $model_has_roles = DB::table('model_has_roles')->get()->map(function($item){
            return (array)$item;
        })->toArray();

        $roles =  DB::table('roles')->get()->map(function($item){
            return (array)$item;
        })->toArray();

        DB::table('permissions')->truncate();
        $modules = [
            Settings::class,
            StockManager::class,
            CustomerManager::class,
            SalesRepManager::class,
            MedReminder::class,
            PushNotification::class,
            CouponManager::class,
            VoucherManager::class,
            OrderManager::class,
            PromotionalManager::class
        ];

        foreach ($modules as $module)
        {
            $_module = $module::CONFIG;
            $permissions = $module::PERMISSION;
            $app_id =  $_module['app_id'];
            unset($_module['app_id']);

            $createdModule = Module::updateOrCreate($_module, Arr::except($_module,['id']));
            $createdModule->apps()->sync($app_id);
            foreach ($permissions as $permission)
            {
                $spatiePermission = Permission::updateOrCreate(['name' => $permission['name'], 'module_id' =>  $createdModule->id, 'guard_name' =>$permission['guard_name'] ], Arr::except($permission,['id', 'app_id']));
                $normalPermission = \App\Models\Permission::find($spatiePermission->id);
                if(isset($permission['app_id'])) {
                    $normalPermission->apps()->sync($permission['app_id']);
                } else {
                    $normalPermission->apps()->sync($app_id);
                }
            }
        }

        $this->line('Permission has been populated successfully');


        if(count($model_has_permissions) > 0) {
            DB::table('model_has_permissions')->truncate();
            DB::table('model_has_permissions')->insert($model_has_permissions);
        }

        if(count($model_has_roles) > 0) {
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_roles')->insert($model_has_roles);
        }

        if(count($roles) > 0) {
            DB::table('roles')->truncate();
            DB::table('roles')->insert($roles);
        }

        Schema::enableForeignKeyConstraints();

        foreach ($this->permissionAppIDS as $appID) {
            Cache::forget('module-with-permission-list-'.$appID);
            Cache::forget('permission-list-'.$appID);
            Cache::clear();
            return Command::SUCCESS;
        }
    }
}
