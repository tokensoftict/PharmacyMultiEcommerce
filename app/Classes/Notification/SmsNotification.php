<?php

namespace App\Classes\Notification;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SmsNotification
{

    /**
     * @throws ConnectionException
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
        Storage::append("sms.txt", $message);
        sendSMS($notifiable->phone, $notifiable, $message);
        return  true;
    }

}
