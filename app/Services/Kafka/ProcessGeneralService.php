<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Models\Classification;
use App\Models\LocalCustomer;
use App\Models\Manufacturer;
use App\Models\NewStockArrival;
use App\Models\Productcategory;
use App\Models\Productgroup;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessGeneralService
{
    public static function handle(ConsumedMessage $message) : void
    {
        $body = $message->getBody();;
        $action =  $body['action'];
        $data = $body[0]['data'];

        switch ($action) {
            case KafkaAction::CREATE_BRAND:
                self::createBrand($data);
                break;
            case KafkaAction::UPDATE_BRAND:
                self::updateBrand($data);
                break;
            case KafkaAction::CREATE_CATEGORY:
                self::createCategory($data);
                break;
            case KafkaAction::UPDATE_CATEGORY:
                self::updateCategory($data);
                break;
            case KafkaAction::CREATE_CLASSIFICATION:
                self::createClassification($data);
                break;
            case KafkaAction::UPDATE_CLASSIFICATION:
                self::updateClassification($data);
                break;
            case KafkaAction::CREATE_CUSTOMER:
                self::createCustomer($data);
                break;
            case KafkaAction::UPDATE_CUSTOMER:
                self::updateCustomer($data);
                break;
            case KafkaAction::CREATE_MANUFACTURER:
                self::createManufacturer($data);
                break;
            case KafkaAction::UPDATE_MANUFACTURER:
                self::updateManufacturer($data);
                break;
            case KafkaAction::NEW_ARRIVAL:
                self::newArrival($data, $body[0]['store']);
                break;
            case KafkaAction::CREATE_STOCK_GROUP:
                self::createStockGroup($data);
                break;
            case KafkaAction::UPDATE_STOCK_GROUP:
                self::updateStockGroup($data);
                break;
        }

    }


    public static function createBrand(array $data) : void
    {

    }

    public static function updateBrand(array $data) : void
    {

    }

    /**
     * @param array $data
     * @return Productcategory|bool
     */
    public static function createCategory(array $data) : Productcategory | bool
    {
        if(isset($data[1])) {
            return DB::table("productcategories")->insert($data);
        } else {
            return Productcategory::create($data);
        }

    }

    /**
     * @param array $data
     * @return Productcategory
     */
    public static function updateCategory(array $data) : Productcategory
    {
        return Productcategory::where("id", $data['id'])->update($data);
    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function createClassification(array $data) : Classification | bool
    {
        if(isset($data[1])) {
            return DB::table("classifications")->insert($data);
        } else {
            return Classification::create($data);
        }

    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function updateClassification(array $data) : Classification | bool
    {
        return Classification::where("id", $data['id'])->update($data);
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function createCustomer(array $data) : LocalCustomer | bool
    {
        if(isset($data[1])) {
            return DB::table("local_customers")->insert($data);
        } else {
            return LocalCustomer::create($data);
        }
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function updateCustomer(array $data) : LocalCustomer | bool
    {
        $localCustomer =  LocalCustomer::where('local_id', $data['local_id'])->first();
        if(!$localCustomer) {
            return self::createCustomer($data);
        }
        return $localCustomer->update($data);
    }

    /**
     * @param array $data
     * @return Manufacturer|bool
     */
    public static function createManufacturer(array $data) : Manufacturer|bool
    {
        if(isset($data[1])) {
            return DB::table("manufacturers")->insert($data);
        } else {
            return Manufacturer::create($data);
        }

    }

    /**
     * @param array $data
     * @return Manufacturer
     */
    public static function updateManufacturer(array $data) : Manufacturer
    {
        return Manufacturer::where("id", $data['id'])->update($data);
    }


    /**
     * @param array $data
     * @param string $store
     * @return bool
     */
    public static function newArrival(array $data, string $store) : bool
    {
        $localStockIDs = array_keys($data);
        $localStock = $data;
        $stocks = Stock::whereIn("local_stock_id", $localStockIDs)->get();
        $newArrivalStocks = $stocks->map(function($stock) use ($store, $localStock){
            return [
                "stock_id" => $stock->id,
                "app_id" => $store,
                "quantity" =>$localStock[$stock->local_stock_id]['qty'],
                "arrival_date" => date("Y-m-d")
            ];
        })->toArray();

        foreach ($newArrivalStocks as $newArrivalStock) {
            NewStockArrival::create($newArrivalStock);
        }

        return true;
    }


    /**
     * @param array $data
     * @return Productgroup|bool
     */
    public static function createStockGroup(array $data) : Productgroup | bool
    {
        if(isset($data[1])) {
            return DB::table("productgroups")->insert($data);
        } else {
            return Productgroup::create($data);
        }
    }


    /**
     * @param array $data
     * @return Productgroup
     */
    public static function updateStockGroup(array $data) : Productgroup
    {
        return Productgroup::where("id", $data['id'])->update($data);
    }

}
