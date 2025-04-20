<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Models\Stock;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessStockService
{
    public static function handle(ConsumedMessage $message) : void
    {
        $body = $message->getBody();;
        $action =  $body['action'];
        $data = $body[0]['data'];

        switch ($action) {
            case KafkaAction::CREATE_STOCK:
                self::createStock($data);
                break;
            case KafkaAction::UPDATE_STOCK:
                self::updateStock($data);
                break;
            default:

        }

    }

    /**
     * @param array $data
     * @return bool|Stock
     */
    public static function createStock(array $data) : bool|Stock
    {
        if(isset($data[1])) {
            // this is a bulk insert so where to use Bulk insertion method
            foreach ($data as $stock){
                $wholesales = $stock['stock_prices']['wholesales'] ?? false;
                $supermarket = $stock['stock_prices']['supermarket'] ?? false;
                unset($data['stock_prices']);
                $pushStock = Stock::updateOrCreate([
                    "local_stock_id" => $stock['local_stock_id']
                ], $stock);

                if($wholesales) {
                    $wholesales = new WholessalesStockPrice($wholesales);
                    $pushStock->wholessales_stock_prices()->save($wholesales);
                }
                if($supermarket) {
                    $supermarket = new SupermarketsStockPrice($supermarket);
                    $pushStock->supermarkets_stock_prices()->save($supermarket);
                }

            }
        } else {
            $pushStock = Stock::create($data);

            $wholesales = $stock['stock_prices']['wholesales'] ?? false;
            $supermarket = $stock['stock_prices']['supermarket'] ?? false;

            if($wholesales) {
                $wholesales = new WholessalesStockPrice($wholesales);
                $pushStock->wholessales_stock_prices()->save($wholesales);
            }
            if($supermarket) {
                $supermarket = new SupermarketsStockPrice($supermarket);
                $pushStock->supermarkets_stock_prices()->save($supermarket);
            }

            return $pushStock;
        }
        return true;
    }

    /**
     * @param array $data
     * @return Stock
     */
    public static function updateStock(array $data) : Stock
    {
        $pushStock = Stock::where("local_stock_id", $data['local_stock_id'])->first();
        $pushStock->update($data);

        $wholesales = $stock['stock_prices']['wholesales'] ?? false;
        $supermarket = $stock['stock_prices']['supermarket'] ?? false;

        if($wholesales) {
            $wholesalesModel =  $pushStock->wholessales_stock_prices ?? new WholessalesStockPrice($wholesales);
            if(isset($wholesalesModel->quantity)) {
                $wholesalesModel->update($wholesales);
            } else {
                $pushStock->wholessales_stock_prices()->save($wholesalesModel);
            }
        }
        if($supermarket) {
            $supermarketModel =  $pushStock->supermarkets_stock_prices ?? new SupermarketsStockPrice($supermarket);
            if(isset($supermarketModel->quantity)) {
                $supermarketModel->update($supermarket);
            } else {
                $pushStock->supermarkets_stock_prices()->save($supermarketModel);
            }
        }

        return $pushStock;
    }
}
