<?php
namespace App\Traits;

use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesAdmin;
use App\Models\WholesalesUser;
use App\Notifications\NewAccountRegistration;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Collection;

trait UserModelTrait
{

    /**
     * @return void
     */
    public final function generateEmailVerificationPin() : void
    {
        $this->email_verification_pin = rand(100000, 999999);
        $this->save();
    }


    /**
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public final function markEmailAsVerified() : bool
    {
        if (in_array('web', request()->route()->gatherMiddleware())) {
            return $this->forceFill([
                'email_verified_at' => $this->freshTimestamp(),
            ])->save();
        }

        if (in_array('api', request()->route()->gatherMiddleware())) {
            if ($this->email_verification_pin == request()->get('otp')) {
                return $this->forceFill([
                    'email_verified_at' => $this->freshTimestamp(),
                ])->save();

            }
        }

        return false;
    }


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

    public final function updateDeviceKey(string|null $key) : void
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

    public function wholesales_admin()
    {
        return $this->hasOne(WholesalesAdmin::class);
    }

    public function supermarket_admin()
    {
        return $this->hasOne(SupermarketAdmin::class);
    }

    public function updateLastSeen()
    {
        $this->last_seen = now();
        $this->save();
    }


    /**
     * @return User
     */
    public static function selfSystem() : User
    {
        return User::find(1);
    }

    public final function medReminderSchedule() : Collection
    {
        return (new MedReminderService())->getMedRemindersLocalNotification($this);
    }
}

