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
        if(config('app.BULKSMS_ENGINE') === "SENDCHAMP") {


            $response = Http::withHeaders([
                'Accept' => 'application/json,text/plain,*/*',
                'Authorization' => config('app.SEND_CHAMP_AUTHORIZATION'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(300)
                ->post(config("app.BULKSMS_URL"), [
                'channel' => 'sms',
                'sender' => config("app.BULKSMS_SENDER"),
                'token_type' => 'numeric',
                'token_length' => (int)config("app.SEND_CHAMP_TOKEN_LENGTH"),
                'expiration_time' => (int)config("app.SEND_CHAMP_EXPIRATION_TIME"),
                'customer_mobile_number' => "234".$notifiable->phone,
                'meta_data' => null,
                'in_app_token' => false,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                $token = $body['data']['token'];

                $notifiable->verification_pin = $token;
                $notifiable->update();
            } else {
                die(json_encode(['status' => false, 'error' => 'There was an error sending message please try again', 'code' => 500]));
            }

        } else {
             Http::post(config("app.BULKSMS_URL"), [
                "email" => config("app.BULKSMS_EMAIL"),
                "password" => config("app.BULKSMS_PASSWORD"),
                "recipient" => $notifiable->phone,
                "message" => $message,
                "senderid" => config("app.BULKSMS_SENDER"),
            ])->getBody()->getContents();
        }
        return  true;
    }

}
