<?php

namespace App\Classes\Notification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SmsNotification
{

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
        Storage::append("sms.txt", $message);
        $response = Http::post(config("app.BULKSMS_URL"), [
            "email" => config("app.BULKSMS_EMAIL"),
            "password" => config("app.BULKSMS_PASSWORD"),
            "recipient" => $notifiable->phone,
            "message" => $message,
            "senderid" => config("app.BULKSMS_SENDER"),
        ])->getBody()->getContents();

        if(isset($response['status']) && $response['status'] == "OK") {
            return  true;
        }else{
            return  $response;
        }
    }

}
