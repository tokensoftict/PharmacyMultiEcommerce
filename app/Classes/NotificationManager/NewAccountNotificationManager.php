<?php

namespace App\Classes\NotificationManager;

use App\Models\User;
use App\Notifications\NewAccountRegistration;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class NewAccountNotificationManager
{

    public static $notifications = [
        NewAccountRegistration::class
    ];

    public static function notifyAll(User $user, array $veryWhat = []) : void
    {
        collect(self::$notifications)->each(function($notificationClass) use($user, $veryWhat){
            $user->notify(new $notificationClass(...$veryWhat));
        });
    }


    /**
     * @param User $user
     * @return void
     */
    public static function applyResendVerificationToNotification(User $user) : void
    {
        NewAccountRegistration::createUrlUsing(function ($notifiable) use ($user){
            $hash = sha1($notifiable->getEmailForVerification());
            $user->update(["verification_token" => $hash]);
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'auth_token' => encrypt(json_encode(["email" => $user->email])),
                    'hash' => $hash,
                ]
            );
        });

        NewAccountRegistration::toMailUsing(function ($notifiable, $verificationUrl) use ($user){
            return (new MailMessage)
                ->subject('Email Verification')
                ->greeting("Hello $notifiable->firstname $notifiable->lastname")
                ->line("Click the button below to confirm your email address.")
                ->action("Verify Email", $verificationUrl)
                ->line(Lang::get("If you did not request this email, you can safely delete this email."))
                ->salutation("Thanks & Regards");
        });

        NewAccountRegistration::createSmsUsing(function ($notifiable) use ($user){
            $otp = mt_rand(1000, 9999);
            $user->verification_pin = $otp;
            $user->update();
            return "Hello ".$notifiable->firstname." Please use $otp to verify your phone number";
        });
    }
}
