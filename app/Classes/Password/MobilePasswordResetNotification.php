<?php

namespace App\Classes\Password;

use App\Classes\Notification\SmsNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class MobilePasswordResetNotification extends Notification
{

    public $pin;
    public $token;

    public function __construct(string $pin, string $token = NULL)
    {
        $this->pin = $pin;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return [SmsNotification::class, 'mail'];
    }

    public function toMail($notifiable)
    {
        if($this->token) {
            $url = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }

        $mail = (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->line(Lang::get('Please use the four digit pin below to reset your **Password**'))
            ->line("**{$this->pin}**");

            if(isset($url)) {
                $mail->line('----**Or**---');
                $mail->line(Lang::get('Click the button below to reset your password.'));
                $mail->action(Lang::get('Reset Password'), $url);
            }

            $mail->line(Lang::get('This password reset pin will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]));
            $mail->line(Lang::get('If you did not request a password reset, no further action is required.'));

        return $mail;
    }


    public function toSms($notifiable)
    {
        return "Please use the four digit pin below to reset your password ".$this->pin;
    }
}
