<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Message\ConsumedMessage;

class TestKafkaStockUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-kafka-stock-update';

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
        $json = '{
            "0": {
                "KAFKA_ACTION": "UPDATE_STOCK",
                "KAFKA_TOPICS": "STOCKS",
                "action": "update",
                "table": "stock",
                "data": [
                    {
                        "local_stock_id": 6,
                        "description": null,
                        "name": "43C ABC TEST",
                        "classification_id": 3,
                        "productcategory_id": 51,
                        "manufacturer_id": 112,
                        "productgroup_id": 347,
                        "box": 5,
                        "is_wholesales": 1,
                        "max": "0",
                        "carton": 100,
                        "sachet": 1,
                        "custom_price": [
                            {"price": 5, "min_qty": 5, "max_qty": 7, "department": "retail"},
                            {"price": 4, "min_qty": 7, "max_qty": 10000, "department": "retail"},
                            {"price": 300, "min_qty": 3, "max_qty": 4, "department": "wholesales"},
                            {"price": 500, "min_qty": 4, "max_qty": 10000, "department": "wholesales"}
                        ],
                        "stock_option_values": [
                            {
                                "optionName": "Size",
                                "option": "select",
                                "option_id": 2,
                                "options": [
                                    {"id": 2, "name": "S", "retail_price_prefix": "+", "retail_price": 5, "retail_status": 1, "wholesales_status": 1, "wholesales_price": 3, "wholesales_price_prefix": "+"},
                                    {"id": 3, "name": "M", "retail_price_prefix": "+", "retail_price": 10, "retail_status": 1, "wholesales_status": 1, "wholesales_price": 8, "wholesales_price_prefix": "+"},
                                    {"id": 6, "name": "L", "retail_price_prefix": "+", "retail_price": 23, "retail_status": 1, "wholesales_status": 1, "wholesales_price": 20, "wholesales_price_prefix": "+"}
                                ]
                            }
                        ],
                        "dependent_products": [
                            {"stock_id": 11834, "parent": 2, "child": 1}
                        ],
                        "stock_prices": {
                            "wholesales": {"app_id": 5, "price": "486.00", "quantity": 19, "status": true, "expiry_date": "2033-01-31"},
                            "supermarket": {"app_id": 6, "price": "111.00", "quantity": 0, "status": true, "expiry_date": null}
                        }
                    },
                    {
                        "local_stock_id": 11834,
                        "description": null,
                        "name": "PEACE AIKI ENERGY DRINK X4 (Classic)",
                        "classification_id": 3,
                        "productcategory_id": 34,
                        "manufacturer_id": 114,
                        "productgroup_id": 347,
                        "box": 1,
                        "is_wholesales": 1,
                        "max": "0",
                        "carton": 100,
                        "sachet": 1,
                        "custom_price": [],
                        "stock_option_values": [],
                        "dependent_products": [],
                        "stock_prices": {
                            "wholesales": {"app_id": 5, "price": "385.00", "quantity": 7391, "status": true, "expiry_date": "2028-07-24"},
                            "supermarket": {"app_id": 6, "price": "460.00", "quantity": 188, "status": true, "expiry_date": "2028-07-30"}
                        }
                    }
                ],
                "url": "https://admin.generaldrugcentre.com/api/data/dataupdate/add_or_update_stock"
            },
            "action": "UPDATE_STOCK"
        }';

        $payload = json_decode($json, true);
        
        $message = new ConsumedMessage(
            topicName: 'STOCKS',
            partition: 0,
            body: $payload,
            key: null,
            headers: [],
            offset: 0,
            timestamp: now()->timestamp
        );

        \App\Services\Kafka\ProcessStockService::handle($message);

        $this->info('Stock update processed successfully.');
    }
}
