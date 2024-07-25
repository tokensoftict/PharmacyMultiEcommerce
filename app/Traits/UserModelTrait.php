<?php
namespace App\Traits;

use App\Models\SalesRepresentative;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Notifications\NewAccountRegistration;
use Illuminate\Auth\Notifications\VerifyEmail;

trait UserModelTrait
{


    /*
     * Override default New Account Verification
     */

    /**
     * @return void
     */
    public final function sendEmailVerificationNotification() : void
    {
        $this->notify(new NewAccountRegistration(true, false));
    }


    public function getNameAttribute()
    {
        return "$this->firstname $this->lastname";
    }

    public final function updateDeviceKey(string $key) : void
    {
        if(empty($key)) return;
        $this->wholesales_user()?->update(["device_key" => $key]);
        $this->supermarket_user()?->update(["device_key" => $key]);
    }

    public function wholesales_user()
    {
        return $this->hasOne(WholesalesUser::class);
    }

    public function sales_representative()
    {
        return $this->hasOne(SalesRepresentative::class);
    }


    public function supermarket_user()
    {
        return $this->hasOne(SupermarketUser::class);
    }


    public function updateLastSeen()
    {
        $this->last_seen = now();
        $this->update();
    }


    /**
     * @return User
     */
    public static function selfSystem() : User
    {
        return User::find(1);
    }
}

