<?php

namespace App\Classes\Notification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class SmsNotification
{

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
         Storage::append("sms.txt", $message);
         return true;

        $api_url = config("app.BULKSMS_URL");
        $link = "?";
        $link.="username=".urlencode(config("app.BULKSMS_EMAIL"));
        $link.="&password=".urlencode(config("app.BULKSMS_PASSWORD"));
        $link.="&mobiles=".urlencode($notifiable->phone);
        $link.="&message=".urlencode($message);
        $link.="&sender=".urlencode(config("app.BULKSMS_SENDER"));
        $url =  $api_url.$link;
        $response = file_get_contents($url);
        $response = json_decode($response,true);
        if(isset($response['status']) && $response['status'] == "OK") {
            return  true;
        }else{
            return  $response;
        }
    }

}
