<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Models\ProductCustomPrice;
use App\Models\Stock;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use Illuminate\Support\Arr;
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
                    $pushStock->wholessales_stock_prices()->delete();
                    $pushStock->wholessales_stock_prices()->save($wholesales);
                }
                if($supermarket) {
                    $customPrices = $supermarket['custom_price'];
                    unset($data['custom_price']);
                    $supermarket = new SupermarketsStockPrice($supermarket);
                    $pushStock->supermarkets_stock_prices()->delete();
                    $pushStock->supermarkets_stock_prices()->save($supermarket);
                    if(count($customPrices) > 0) {
                        $pushStock->stockquantityprices()->delete();
                        $custom_price = [];
                        foreach ($customPrices as $customPrice) {
                            $custom_price[] = new ProductCustomPrice($customPrice);
                        }
                        $pushStock->stockquantityprices()->saveMany($custom_price);
                    }
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
                $customPrices = $supermarket['custom_price'];
                unset($data['custom_price']);
                $supermarket = new SupermarketsStockPrice($supermarket);
                $pushStock->supermarkets_stock_prices()->save($supermarket);
                if(count($customPrices) > 0) {
                    $pushStock->stockquantityprices()->delete();
                    $custom_price = [];
                    foreach ($customPrices as $customPrice) {
                        $custom_price[] = new ProductCustomPrice($customPrice);
                    }
                    $pushStock->stockquantityprices()->saveMany($custom_price);
                }
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
        $stockUpdate = Arr::only($data, ['local_stock_id', 'description', 'name', 'classification_id', 'productcategory_id', 'manufacturer_id', 'productgroup_id', 'box', 'max', 'carton', 'sachet']);
        $pushStock = Stock::with(['wholessales_stock_prices', 'supermarkets_stock_prices'])->where("local_stock_id", $stockUpdate['local_stock_id'])->first();
        $pushStock->update($stockUpdate);

        $wholesales = $data['stock_prices']['wholesales'] ?? false;
        $supermarket = $data['stock_prices']['supermarket'] ?? false;

        if($wholesales) {
            $wholesalesModel =  $pushStock?->wholessales_stock_prices()->first() ?? new WholessalesStockPrice($wholesales);
            if(isset($pushStock?->wholessales_stock_prices)) {
                $wholesalesModel->update($wholesales);
            } else {
                $pushStock->wholessales_stock_prices()->save($wholesalesModel);
            }
        }
        if($supermarket) {
            $supermarketModel =  $pushStock?->supermarkets_stock_prices()->first() ?? new SupermarketsStockPrice($supermarket);
            if(isset($pushStock?->supermarkets_stock_prices)) {
                $supermarketModel->update($supermarket);
            } else {
                $pushStock->supermarkets_stock_prices()->save($supermarketModel);
            }
        }

        return $pushStock;
    }
}
