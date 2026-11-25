<?php

namespace App\Listeners\Opay;

use App\Events\Opay\OpayWebhookReceived;
use App\Services\Opay\OpayWebhookKafkaPublisher;

/**
 * Forwards every OPay webhook received by `mystore` to Kafka so
 * `PSGeneralDrugCentreKafka` receives and stores it locally.
 */
class ForwardOpayWebhookToKafkaListener
{
    public function __construct(
        private readonly OpayWebhookKafkaPublisher $publisher,
    ) {
    }

    public function handle(OpayWebhookReceived $event): void
    {
        $this->publisher->publish(
            data: $event->data,
            headers: $event->headers,
            log: $event->log,
        );
    }
}
