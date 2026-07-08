<?php

namespace App\Classes;

use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesAdmin;
use App\Models\WholesalesUser;
use Reliese\Database\Eloquent\Model;

class AppLists
{

    public static function getApp(WholesalesUser|SalesRepresentative|SupermarketUser|WholesalesAdmin|SupermarketAdmin $model) : string
    {
        $apps = [
            WholesalesUser::class => config('app.WHOLESALES_DOMAIN')[0],
            SalesRepresentative::class => config('app.SALES_REPRESENTATIVES')[0],
            SupermarketUser::class => config('app.SUPERMARKET_DOMAIN')[0],
            WholesalesAdmin::class => config('app.WHOLESALES_ADMIN')[0],
            SupermarketAdmin::class => config('app.SUPERMARKET_ADMIN')[0],
        ];

        return $apps[get_class($model)];
    }

    /**
     * @param string $domain
     * @return string
     */
    public static function getAppModelByDomain(string $domain) : string
    {
        $apps = [
            config('app.WHOLESALES_DOMAIN')[0] => WholesalesUser::class,
            config('app.SUPERMARKET_DOMAIN')[0] => SupermarketUser::class,
            config('app.SALES_REPRESENTATIVES')[0] => SalesRepresentative::class,
            config('app.WHOLESALES_ADMIN')[0] => WholesalesAdmin::class,
            config('app.SUPERMARKET_ADMIN')[0] => SupermarketAdmin::class,
            config('app.ADMIN_DOMAIN')[0] => User::class,
            config('app.AUTH_DOMAIN')[0] => User::class,
            config('app.PUSH_DOMAIN')[0] => User::class,
            config('app.IMAGES_DOMAIN')[0] => User::class,
        ];

        return $apps[$domain];
    }


    /**
     * @param string $domain
     * @param User $user
     * @return User|WholesalesUser
     */
    public static function insertAppModelByDomain(string $domain, User $user)
    {
        return match ($domain) {
            config('app.WHOLESALES_ADMIN')[0] => WholesalesAdmin::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
                'invitation_status' => true,
                'invitation_sent_date' => now()->format("Y-m-d"),
                'invitation_approval_date' => now()->format("Y-m-d"),
                'added_by' => $user->id
            ]),
            config('app.SUPERMARKET_ADMIN')[0] => SupermarketAdmin::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
                'invitation_status' => true,
                'invitation_sent_date' => now()->format("Y-m-d"),
                'invitation_approval_date' => now()->format("Y-m-d"),
                'added_by' => $user->id
            ]),
            config('app.ADMIN_DOMAIN')[0] => $user,
            config('app.AUTH_DOMAIN')[0] => $user,
            config('app.PUSH_DOMAIN')[0] => $user,
            config('app.IMAGES_DOMAIN')[0] => $user,
            config('app.WHOLESALES_DOMAIN')[0] => WholesalesUser::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
            ]),
            config('app.SUPERMARKET_DOMAIN')[0] => SupermarketUser::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
            ]),
            config('app.SALES_REPRESENTATIVES')[0] => SalesRepresentative::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
                'invitation_status' => true,
                'invitation_sent_date' => now()->format("Y-m-d"),
                'invitation_approval_date' => now()->format("Y-m-d"),
                'added_by' => $user->id
            ]),
            default => false
        };
    }

}
