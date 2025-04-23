<?php

namespace Database\Seeders;

use App\Classes\AppLists;
use App\Models\App;
use App\Models\AppUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Facade as Avatar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function(){

            if (!file_exists(public_path('storage/users'))) {
                mkdir(public_path('storage/users'), 0777, true);
            }

            Avatar::create(config('app.SUPER_ADMINISTRATOR'))->save(public_path('storage/users/1.png'));

            DB::table('users')->insert([
                'firstname' => 'General',
                'lastname' => 'Drug',
                'email' => 'info@generaldrugcentre.com',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => bcrypt(123456),
                'image' => 'storage/users/1.png',
                'phone' => '08011111111'
            ]);

            $user = User::where('email', 'admin@store.com')->first();

            if($user){
                $user->assignRole(config('app.SUPER_ADMINISTRATOR'));
            }

            $apps = App::where('show', true)->get();

            foreach ($apps as $app){
                $appLink = AppLists::insertAppModelByDomain($app->domain, $user);
                if(!$appLink) continue;
                AppUser::create([
                    'domain' => $app->domain,
                    'user_id' => $user->id,
                    'app_id' => $app->id,
                    'user_type_type' => $appLink::class,
                    'user_type_id' => $appLink->id
                ]);
            }

        });
    }
}
