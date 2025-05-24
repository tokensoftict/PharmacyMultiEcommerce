<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use Illuminate\Console\Command;

class SendPushNotificationToClientsPhone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-push-notification-to-clients-phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notifications = PushNotification::query()->where('status', 'APPROVED')->get();
        foreach ($notifications as $notification) {
            sendNotificationToDevice($notification);
            $notification->status = 'SENT';
            $notification->save();
        }
    }
}
