<?php

namespace App\Listeners;


use App\Events\Order\OrderStatusUpdatedEvent;
use App\Mail\Order\NewOrderEmail;
use Illuminate\Support\Facades\Mail;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class OrderStatusUpdateListener
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
    public function handle(OrderStatusUpdatedEvent $event): void
    {

    }
}
