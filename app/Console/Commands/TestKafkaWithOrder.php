<?php

namespace App\Console\Commands;

use App\Enums\KafkaAction;
use App\Enums\KafkaEvent;
use App\Enums\KafkaTopics;
use App\Models\Order;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use function Laravel\Prompts\text;
class TestKafkaWithOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-kafka-with-order';

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
        $orderId = text(
            label: 'Enter the Order Id, you want to Push?',
            placeholder: 'E.g. 1,2,3',
            hint: 'The Order Id now..........'
        );

        $order = Order::with([
            'customer',
            'address',
            'address.town',
            'address.country',
            'address.state',
            'status',
            'order_total_orders',
            'payment_method',
            'delivery_method',
            'order_products'
        ])->find($orderId);

        $message = new Message(
            headers: ['event' => KafkaEvent::ONLINE_PUSH],
            body: ['order' => $order->toArray(), "action" => KAFKAAction::PROCESS_ORDER],
            key: config('app.KAFKA_HEADER_KEY')
        );

        Kafka::publish()->onTopic(KafkaTopics::ORDERS)->withMessage($message)->send();

        $this->info('Order Id: ' . $orderId);
    }
}
