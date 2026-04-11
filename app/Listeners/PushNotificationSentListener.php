<?php
namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use NotificationChannels\Fcm\FcmChannel;

class PushNotificationSentListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        if ($event->channel == FcmChannel::class) {
            if (isset($event->notification->customerPushNotification)) {
                $event->notification->customerPushNotification->status_id = status('Complete');
                $event->notification->customerPushNotification->save();
            }
        }
    }
}
