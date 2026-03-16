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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessGeneralService
{
    public static function handle(ConsumedMessage $message): void
    {
        $body = $message->getBody();
        $action = $body[0]['KAFKA_ACTION'];
        Log::info($action);
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
            case KafkaAction::EARNED_LOYALTY:
                self::updateUserLoyalty($data);
                break;
        }

    }


    public static function createBrand(array $data): void
    {

    }

    public static function updateBrand(array $data): void
    {

    }

    /**
     * @param array $data
     * @return Productcategory|bool
     */
    public static function createCategory(array $data): Productcategory|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("productcategories")->truncate();
            $result = DB::table("productcategories")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Productcategory::create($data);
        }

    }

    /**
     * @param array $data
     * @return Productcategory
     */
    public static function updateCategory(array $data): Productcategory
    {
        return Productcategory::where("id", $data['id'])->update($data);
    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function createClassification(array $data): Classification|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("classifications")->truncate();
            $result = DB::table("classifications")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Classification::create($data);
        }

    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function updateClassification(array $data): Classification|bool
    {
        Schema::disableForeignKeyConstraints();
        DB::table("classifications")->truncate();
        $result = Classification::where("id", $data['id'])->update($data);
        Schema::enableForeignKeyConstraints();
        return $result;
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function createCustomer(array $data): LocalCustomer|bool
    {
        if (isset($data[1])) {
            $result = DB::table("local_customers")->insert($data);
            foreach ($data as $customer) {
                self::updateUserLocalId($customer);
            }
            return $result;
        }
        else {
            $customer = LocalCustomer::create($data);
            self::updateUserLocalId($data);
            return $customer;
        }
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function updateCustomer(array $data): LocalCustomer|bool
    {
        if (isset($data[1])) {
            foreach ($data as $customer) {
                $localCustomer = LocalCustomer::where('local_id', $customer['local_id'])->first();
                if (!$localCustomer) {
                    self::updateUserLocalId($customer);
                    return self::createCustomer($customer);
                }
                self::updateUserLocalId($customer);
                $localCustomer->update($customer);
            }
            return true;
        }
        else {
            $localCustomer = LocalCustomer::where('local_id', $data['local_id'])->first();
            if (!$localCustomer) {
                self::updateUserLocalId($data);
                return self::createCustomer($data);
            }

            self::updateUserLocalId($data);
            return $localCustomer->update($data);
        }

    }

    /**
     * @param array $data
     * @return Manufacturer|bool
     */
    public static function createManufacturer(array $data): Manufacturer|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("manufacturers")->truncate();
            $result = DB::table("manufacturers")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Manufacturer::create($data);
        }

    }

    /**
     * @param array $data
     * @return Manufacturer
     */
    public static function updateManufacturer(array $data): Manufacturer|bool
    {
        return Manufacturer::where("id", $data['id'])->update($data);
    }


    /**
     * @param array $data
     * @param string $store
     * @return bool
     */
    public static function newArrival(array $data, string $store): bool
    {
        $localStockIDs = array_keys($data);
        $localStock = $data;
        $stocks = Stock::whereIn("local_stock_id", $localStockIDs)->get();
        $newArrivalStocks = $stocks->map(function ($stock) use ($store, $localStock) {
            return [
            "stock_id" => $stock->id,
            "app_id" => $store,
            "quantity" => $localStock[$stock->local_stock_id]['qty'],
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
    public static function createStockGroup(array $data): Productgroup|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("productgroups")->truncate();
            $result = DB::table("productgroups")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Productgroup::create($data);
        }
    }


    /**
     * @param array $data
     * @return Productgroup
     */
    public static function updateStockGroup(array $data): Productgroup|bool
    {
        return Productgroup::where("id", $data['id'])->update($data);
    }


    public static function updateUserLocalId($data): bool
    {
        $user = User::where('phone', $data['phone_number'])->first();
        if ($user) {
            $user->update([
                'local_id' => $data['local_id'],
                'loyalty_points' => $data['loyalty_points'],
            ]);
            return true;
        }
        return false;
    }



    public static function updateUserLoyalty($data): bool
    {
        $user = User::where('local_id', $data['local_id'])->first();
        if ($user) {
            $user->update([
                'loyalty_points' => $data['loyalty_points'],
            ]);

            //trigger push notification for the customer

            return true;
        }
        return false;
    }

}