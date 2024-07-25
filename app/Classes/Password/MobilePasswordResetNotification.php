<?php

namespace App\Classes\Password;

use App\Classes\Notification\SmsNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class MobilePasswordResetNotification extends Notification
{

    public $pin;

    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    public function via($notifiable)
    {
        return [SmsNotification::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->line(Lang::get('Please use the four digit pin below to reset your password'))
            ->line($this->pin)
            ->line(Lang::get('This password reset pin will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }


    public function toSms($notifiable)
    {
        return "Please use the four digit pin below to reset your password ".$this->pin;
    }
}
