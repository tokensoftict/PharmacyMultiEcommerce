<?php

namespace App\Notifications;

use App\Models\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DevicePushNotification extends Notification //implements ShouldQueue
{
    use Queueable;

    public PushNotification $pushNotification;
    public $customerPushNotification = null;
    /**
     * Create a new notification instance.
     */
    public function __construct(PushNotification $pushNotification, $customerPushNotification)
    {
        $this->pushNotification = $pushNotification;
        $this->customerPushNotification = $customerPushNotification;
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
        $data ['notificationType'] = $this->pushNotification->action ?? "DEFAULT";
        if($this->pushNotification->stocks->count() > 0) {
            $stocks = $this->pushNotification->stocks->map(function ($stock) {
                return $stock['stock_id'];
            })->toArray();
            $data['data'] = "I am strings";

        }

        if(!is_null($this->pushNotification->payload)) {
            $data ['extra'] = json_encode($this->pushNotification->payload);
        }

        //setEnvironment

        $data['environment'] = match ( $this->pushNotification->app->model_id) {
            6 => "supermarket",
            4 => "sales_representative",
            default => "wholesales"
        };



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


        if($this->customerPushNotification and $this->customerPushNotification->customer) {
            $this->customerPushNotification->status_id = status('Complete');
            $this->customerPushNotification->save();
        }

        return $fcm;
    }
}
