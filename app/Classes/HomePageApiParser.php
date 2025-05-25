<?php

namespace App\Classes;

use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Classification;
use App\Models\Manufacturer;
use App\Models\NewStockArrival;
use App\Models\Stock;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomePageApiParser
{

    /**
     * @param array $product
     * @return AnonymousResourceCollection
     */
    public static function parseProductType(array $product) : AnonymousResourceCollection|array
    {
        return match($product['type']){
            "classifications" => self::getProductByClassification($product['id'], $product['limit']),
            "productcategories" => self::getProductByProductCategories($product['id'], $product['limit']),
            "manufacturers" => self::getProductByManufacturer($product['id'], $product['limit']),
            "new_arrivals" => self::newArrivals($product['limit']),
            "topBrands" => self::topBrands(),
            "lowestClassifications" => self::lowestClassifications(),
            "ImageSlider" => self::ImageSlider(),
            default => []
        };
    }


    /**
     * @param int $id
     * @param int $limit
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function getProductByClassification(int $id, int $limit =15) : AnonymousResourceCollection
    {
        return StockListResource::collection(Stock::where('classification_id', $id)->limit($limit)->get());
    }


    /**
     * @param int $id
     * @param int $limit
     * @return AnonymousResourceCollection
     */
    public static function getProductByManufacturer(int $id, int $limit =15) : AnonymousResourceCollection
    {
        return StockListResource::collection(Stock::where('manufacturer_id', $id)->limit($limit)->get());
    }


    /**
     * @param int $id
     * @param int $limit
     * @return AnonymousResourceCollection
     */
    public static function getProductByProductCategories(int $id, int $limit =15) : AnonymousResourceCollection
    {
        return StockListResource::collection(Stock::where('productcategory_id', $id)->limit($limit)->get());
    }


    /**
     * @param int $limit
     * @return AnonymousResourceCollection
     */
    public static function newArrivals(int $limit =15) : AnonymousResourceCollection
    {
        $stockID =  NewStockArrival::query()->orderBy("id", "DESC")
            ->where('app_id', ApplicationEnvironment::$id)->limit($limit)
            ->pluck("stock_id")->toArray();

        return StockListResource::collection(
            Stock::whereIn('id', $stockID)->get()
        );
    }

    /**
     * @return array
     */
    public static function topBrands() : array
    {
        $manufacturers = Manufacturer::whereHas('media')->get();
        return [
            "id" => mt_rand(),
            "brands" => GeneralResource::collection($manufacturers),
            "label" => "Top Brands",
        ];
    }


    public static function lowestClassifications() : array
    {
        $classifications = [
            "Injections" => "💉",
            "Anti-Ulcer Kit" => "🩺🫃",
            "Multivitamins" => "💊🍊",
            "Antidiabetic" => "🩸🧪",
            "Baby Supplement" => "👶🍼",
            "Antitubercular" => "🫁💊"
        ];
        $specialClassifications = Classification::whereIn("name", array_keys($classifications))->get();
        $lowestPriceClassification = [];
        foreach ($specialClassifications as $specialClassification) {

            $stock = $specialClassification->stocks()
                ->with(ApplicationEnvironment::$stock_model_string)
                ->get()
                ->sortBy(function ($stock) {
                    return optional($stock->{ApplicationEnvironment::$stock_model_string})->price;
                })
                ->first();

            if ($stock) {
                $lowestPriceClassification[] =[
                    "id" => $specialClassification->id,
                    "price" => money($stock->{ApplicationEnvironment::$stock_model_string}->price),
                    "name" => $specialClassification->name,
                    "icon"  => $classifications[$specialClassification->name],
                    "seeAll" => "stock/".$specialClassification->id."/by_classification"
                ];
            }
        }
        return $lowestPriceClassification;
    }


    public static function ImageSlider() :array
    {
        return [
            [
                "name" => "Injections 💉",
                "id" => mt_rand(),
                "banner" => "https://eu2.contabostorage.com/49e6c10078694e4e91ffb15941b37dc9:psgdc/psgdc/5032/slide-1.jpeg",
                "seeAll" => "stock/49/by_classification"
            ],
            [
                "name" => "Multivitamins 💊🍊",
                "id" => mt_rand(),
                "banner" => "https://eu2.contabostorage.com/49e6c10078694e4e91ffb15941b37dc9:psgdc/psgdc/5033/slide-2.jpeg",
                "seeAll" => "stock/9/by_classification"
            ]
        ];
    }
}
