<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Models\ProductCustomPrice;
use App\Models\Stock;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessStockService
{
    public static function handle(ConsumedMessage $message): void
    {
        $body = $message->getBody();
        $action = $body['action'];
        $data = $body[0]['data'];
        Log::info($data);
        switch ($action) {
            case KafkaAction::CREATE_STOCK:
                self::createStock($data);
                break;
            case KafkaAction::UPDATE_STOCK:
                self::updateMultipleOrSingleStock($data);
                break;
            case KafkaAction::UPLOAD_IMAGE:
                self::uploadImage($body[0]);
                break;
            default:

        }

    }

    /**
     * @param array $data
     * @return bool|Stock
     */
    public static function createStock(array $data): bool|Stock
    {
        if (isset($data[0]['local_stock_id'])) {
            // this is a bulk insert so where to use Bulk insertion method
            foreach ($data as $stockData) {
                self::updateStock($stockData);
            }
        }
        else {
            return self::updateStock($data);
        }
        return true;
    }


    /**
     * @param array $stockData
     * @return void
     */
    public static function updateMultipleOrSingleStock(array $stockData): void
    {
        if (isset($stockData[0])) { // check if there are multiple item in the data
            foreach ($stockData as $data) {
                self::updateStock($data);
            }
        }
        else {
            self::updateStock($stockData);
        }
    }
    /**
     * @param array $data
     * @return Stock
     */
    public static function updateStock(array $data): Stock
    {
        $stockUpdate = Arr::only($data, ['local_stock_id', 'description', 'name', 'classification_id', 'productcategory_id', 'manufacturer_id', 'productgroup_id', 'box', 'max', 'carton', 'sachet', 'is_wholesales']);

        if (!isset($stockUpdate['local_stock_id'])) {
            Storage::append("stockLog", "Missing local_stock_id: " . json_encode($data));
            return new Stock();
        }

        $pushStock = Stock::where("local_stock_id", $stockUpdate['local_stock_id'])->first();

        if ($pushStock) {
            $pushStock->update($stockUpdate);
        }
        else {
            $pushStock = Stock::create($stockUpdate);
        }

        // Handle Wholesales Stock Price
        if (isset($data['stock_prices']['wholesales'])) {
            $wholesales = $data['stock_prices']['wholesales'];
            $pushStock->wholessales_stock_prices()->updateOrCreate(
            ['app_id' => $wholesales['app_id']],
                $wholesales
            );
        }

        // Handle Supermarket Stock Price
        if (isset($data['stock_prices']['supermarket'])) {
            $supermarket = $data['stock_prices']['supermarket'];
            $pushStock->supermarkets_stock_prices()->updateOrCreate(
            ['app_id' => $supermarket['app_id']],
                $supermarket
            );
        }

        // Handle Custom Prices
        if (isset($data['custom_price'])) {
            self::saveCustomPrices($data['custom_price'], $pushStock);
        }

        // Handle Dependent Products
        if (isset($data['dependent_products'])) {
            self::saveDependentProducts($data['dependent_products'], $pushStock);
        }

        // Handle Stock Option Values
        if (isset($data['stock_option_values'])) {
            self::saveStockOptionValues($data['stock_option_values'], $pushStock);
        }

        return $pushStock;
    }


    /**
     * @param array $customPrices
     * @param Stock $pushStock
     * @return void
     */
    public static function saveCustomPrices(array $customPrices, Stock $pushStock): void
    {
        $pushStock->stockquantityprices()->delete();
        foreach ($customPrices as $customPrice) {
            $pushStock->stockquantityprices()->create($customPrice);
        }
    }

    /**
     * @param array $dependentProducts
     * @param Stock $pushStock
     * @return void
     */
    public static function saveDependentProducts(array $dependentProducts, Stock $pushStock): void
    {
        $pushStock->dependent_products()->delete();
        foreach ($dependentProducts as $dependentProduct) {
            $pushStock->dependent_products()->create([
                'dependent_local_stock_id' => $dependentProduct['stock_id'],
                'parent' => $dependentProduct['parent'] ?? 1,
                'child' => $dependentProduct['child'] ?? 1,
            ]);
        }
    }

    /**
     * @param array $optionValues
     * @param Stock $pushStock
     * @return void
     */
    public static function saveStockOptionValues(array $optionValues, Stock $pushStock): void
    {
        $pushStock->stock_option_values()->delete();
        foreach ($optionValues as $optionValue) {
            $pushStock->stock_option_values()->create([
                'option_name' => $optionValue['optionName'],
                'option_type' => $optionValue['option'] ?? 'select',
                'option_id' => $optionValue['option_id'],
                'options' => $optionValue['options'],
            ]);
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public static function uploadImage(array $data): void
    {
        $localStockId = $data['local_stock_id'];
        $imagePath = $data['image_path'];

        $stock = Stock::where('local_stock_id', $localStockId)->first();
        Log::info($data);
        if ($stock) {
            try {
                $imageFolder = stock_image_folder();

                // Add media from Contabo disk
                $media = $imageFolder
                    ->addMediaFromDisk($imagePath, 'contabo')
                    ->preservingOriginal()
                    ->toMediaCollection('medialibrary');

                // Link media to Stock
                \App\Models\StockMedia::updateOrCreate([
                    'stock_id' => $stock->id,
                    'media_id' => $media->id,
                ], [
                    'stock_id' => $stock->id,
                    'media_id' => $media->id,
                ]);

                Log::info("Image synchronized for stock locally: {$stock->id} (local: {$localStockId})");
            }
            catch (\Exception $e) {
                Log::error("Error synchronizing image for stock {$localStockId}: " . $e->getMessage());
            }
        }
        else {
            Log::warning("Stock not found for image synchronization: {$localStockId}");
        }
    }
}