<?php

namespace App\Classes\Password;
trait CanResetPasswordByMobile
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getPhoneForPasswordReset()
    {
        return $this->phone;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendMobilePinForPasswordReset($pin)
    {
        $this->notify(new MobilePasswordResetNotification($pin, NULL));
    }


    public function sendEmailAndMobileNotificationForPasswordReset(string $pin, string $token)
    {
        $this->notify(new MobilePasswordResetNotification($pin, $token));
    }
}
