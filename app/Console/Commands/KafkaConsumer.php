<?php

namespace App\Console\Commands;

use App\Enums\KafkaTopics;
use App\Services\Kafka\ProcessGeneralService;
use App\Services\Kafka\ProcessOrderService;
use App\Services\Kafka\ProcessStockService;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Exceptions\ConsumerException;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;

class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume';
    protected $description = 'Consume messages from Kafka topics';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer = Kafka::consumer([KafkaTopics::ORDERS, KafkaTopics::STOCKS, KafkaTopics::GENERAL])
            ->withHandler(function (ConsumedMessage $message) {
                // Process the incoming message
                $topic = $message->getTopicName();
                switch ($topic) {
                    case KafkaTopics::ORDERS:
                        ProcessOrderService::handle($message);
                        break;
                    case KafkaTopics::STOCKS:
                        ProcessStockService::handle($message);
                        break;
                    case KafkaTopics::GENERAL:
                        ProcessGeneralService::handle($message);
                        break;
                    default:
                }

                // Handle the message payload as needed
            })
            ->build();
        try {
            $consumer->consume();
        } catch (Exception|ConsumerException $e) {
            report($e);
        }

    }
}
