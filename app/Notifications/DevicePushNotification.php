<?php

namespace App\Notifications;

use App\Models\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DevicePushNotification extends Notification
{
    use Queueable;

    public PushNotification $pushNotification;
    /**
     * Create a new notification instance.
     */
    public function __construct(PushNotification $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $fcm = (new FcmMessage(notification: new FcmNotification(
            title: $this->pushNotification->title,
            body: $this->pushNotification->body,
            image: NULL
        )));

        $fcm->name("{$this->pushNotification->title}");

        $data = [];
        $data ['action'] = $this->pushNotification->action;
        if($this->pushNotification->stocks->count() > 0) {
            $stocks = $this->pushNotification->stocks->map(function ($stock) {
                return $stock['stock_id'];
            })->toArray();
            $data['data'] = json_encode($stocks);

        }

        if(!is_null($this->pushNotification->payload)) {
            $data ['payload'] = json_encode($this->pushNotification->payload);
        }

        $fcm->data($data);


        $fcm->custom([
            'android' => [
                'notification' => [
                    'color' => '#FFFFFF',
                    'sound' => 'default',
                ],
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ],
                ],
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
        ]);

        $fcm->token($notifiable->device_key);


        return $fcm;
    }
}
