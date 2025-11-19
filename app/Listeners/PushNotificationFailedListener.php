<?php

namespace App\Listeners;

use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Support\Facades\Storage;

class PushNotificationFailedListener
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
    public function handle(NotificationFailed $event): void
    {
        if ($event->channel == FcmChannel::class) {

            $report = Arr::get($event->data, 'report');

            $target = $report->target();

            $wholesale = WholesalesUser::query()->where('device_key', $target->value())
                ->first();

            $supermarket = SupermarketUser::query()->where('device_key', $target->value())
                ->first();

            if($supermarket) {
                $supermarket->device_key = NULL;
                $supermarket->save();
            }

            if($wholesale) {
                $wholesale->device_key = NULL;
                $wholesale->save();
            }

            Storage::append('logs/invalid_tokens.txt', $target->value());

            if($wholesale) {
                Storage::append('logs/wholesales.txt', $wholesale->id);
            }

            if($supermarket) {
                Storage::append('logs/supermarket.txt', $supermarket->id);
            }
        }
    }
}
