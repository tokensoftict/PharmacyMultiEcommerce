<?php

namespace App\Classes;

use App\Models\SalesRepresentative;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use Reliese\Database\Eloquent\Model;

class AppLists
{

    public static function getApp(WholesalesUser|SalesRepresentative|SupermarketUser $model) : string
    {
        $apps = [
            WholesalesUser::class => config('app.WHOLESALES_DOMAIN'),
            SalesRepresentative::class => config('app.SALES_REPRESENTATIVES'),
            SupermarketUser::class => config('app.SUPERMARKET_DOMAIN'),
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
            config('app.WHOLESALES_DOMAIN') => WholesalesUser::class,
            config('app.SUPERMARKET_DOMAIN') => SupermarketUser::class,
            config('app.SALES_REPRESENTATIVES') => SalesRepresentative::class,
            config('app.WHOLESALES_ADMIN') => User::class,
            config('app.SUPERMARKET_ADMIN') => User::class,
            config('app.ADMIN_DOMAIN') => User::class,
            config('app.AUTH_DOMAIN') => User::class,
            config('app.PUSH_DOMAIN') => User::class,
            config('app.IMAGES_DOMAIN') => User::class,
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
            config('app.WHOLESALES_ADMIN') => $user,
            config('app.SUPERMARKET_ADMIN') => $user,
            config('app.ADMIN_DOMAIN') => $user,
            config('app.AUTH_DOMAIN') => $user,
            config('app.PUSH_DOMAIN') => $user,
            config('app.IMAGES_DOMAIN') => $user,
            config('app.WHOLESALES_DOMAIN') => WholesalesUser::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
            ]),
            config('app.SUPERMARKET_DOMAIN') => SupermarketUser::create([
                'user_id' => $user->id,
                'status' => $user->email_verified_at != NULL ? 1 : 0,
            ]),
            config('app.SALES_REPRESENTATIVES') => SalesRepresentative::create([
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
