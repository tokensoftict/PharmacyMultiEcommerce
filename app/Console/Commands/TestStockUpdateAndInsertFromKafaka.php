<?php

namespace App\Console\Commands;

use App\Services\Kafka\ProcessStockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Junges\Kafka\Message\ConsumedMessage;

class TestStockUpdateAndInsertFromKafaka extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-stock-update-and-insert-from-kafka';

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
        $kafkaMessage = Storage::json('stock_update.json');
        $message = new ConsumedMessage(
            topicName: $kafkaMessage[0]['KAFKA_TOPICS'],
            partition: 0,
            headers: [],
            body: $kafkaMessage,
            key: 'PSGDC',
            offset: 30,
            timestamp: now()->timestamp
        );
        ProcessStockService::handle($message);
    }
}
