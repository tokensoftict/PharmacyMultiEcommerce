<?php

namespace App\Notifications;

use App\Classes\Notification\SmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class NewAccountRegistration extends Notification
{
    use Queueable;

    public bool $email;

    public bool $phone;
    public static $createUrlCallback;

    public static $createSmsCallback;
    public static $toMailCallback;

    public static $toSmsCallback;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $phone, bool $email)
    {
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];

        if($this->email) $via[] = "mail";
        if($this->phone) $via[] = SmsNotification::class;

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);


        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return $this->buildMailMessage($verificationUrl, $notifiable);
    }



    public function toSms(object $notifiable)
    {
        if (static::$createSmsCallback) {
            return call_user_func(static::$createSmsCallback, $notifiable);
        }

        $otp = mt_rand(1000, 9999);
        $notifiable->verification_pin = $otp;
        $notifiable->update();

        return "Hello ".$notifiable->firstname." Please use $otp to verify your phone number";
    }

    protected function buildMailMessage($url, $notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Email Verification')
            ->greeting("Hello $notifiable->firstname $notifiable->lastname")
            ->line("Click the button below to confirm your email address.");

        if(!is_null($notifiable->email_verification_pin)){
            $mail->line("Or use this 6-digit verification code:")
                ->line("**{$notifiable->email_verification_pin}**") ;
        }

        $mail ->action("Verify Email", $url)
            ->line(Lang::get("If you did not request this email, you can safely delete this email."))
            ->salutation("Thanks & Regards");

        return $mail;

    }

    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }
        $notifiable->generateEmailVerificationPin();
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'auth_token' => encrypt(json_encode(["email" => $notifiable->email])),
            ]
        );
    }


    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    public static function createSmsUsing($callback)
    {
        static::$createSmsCallback = $callback;
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    public static function toSmsUsing($callback)
    {
        static::$toSmsCallback = $callback;
    }
}
