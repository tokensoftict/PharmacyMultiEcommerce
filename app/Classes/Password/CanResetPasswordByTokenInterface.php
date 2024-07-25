<?php

namespace App\Classes\Password;

interface CanResetPasswordByTokenInterface
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getPhoneForPasswordReset();

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendMobilePinForPasswordReset($token);
}
