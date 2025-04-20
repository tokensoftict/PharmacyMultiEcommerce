<?php

namespace App\Services\User;

use App\Classes\AppLists;
use App\Models\App;
use App\Models\AppUser;
use App\Models\SalesRepresentative;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use Illuminate\Support\Arr;

class AppUserService
{
    public final function createAppUser(User $user, WholesalesUser|SupermarketUser|SalesRepresentative|User $attachUser) : AppUser
    {
        $data = [
            'user_id' => $user->id,
            'domain' =>AppLists::getApp($attachUser),
            'user_type_type' => get_class($attachUser),
            'user_type_id' => $attachUser->id,
            'app_id' => App::where("domain", AppLists::getApp($attachUser))->first()->id
        ];

        return AppUser::updateOrCreate($data, $data);
    }

}
