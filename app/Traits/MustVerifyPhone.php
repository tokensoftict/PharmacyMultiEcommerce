<?php
namespace App\Traits;

use App\Notifications\NewAccountRegistration;

trait MustVerifyPhone
{
    /**
     * @return bool
     */
    public final function hasVerifiedPhone() : bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * @return bool
     */
    public final function markPhoneAsVerified(string $otp) : bool
    {
        if($this->verification_pin == $otp) {
            return $this->forceFill([
                'phone_verified_at' => $this->freshTimestamp(),
            ])->save();

        }


        return false;
    }

    /**
     * @return void
     */
    public final function sendPhoneVerificationNotification() : void
    {
        $this->notify(new NewAccountRegistration(false, true));
    }


    /**
     * @return string
     */
    public final function getPhoneForVerification() : string
    {
        return $this->phone;
    }
}
