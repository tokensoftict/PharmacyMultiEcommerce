<?php

namespace App\Classes;

use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Stock;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomePageApiParser
{

    /**
     * @param array $product
     * @return AnonymousResourceCollection
     */
    public static function parseProductType(array $product) : AnonymousResourceCollection
    {
        return match($product['type']){
            "classifications" => self::getProductByClassification($product['id'], $product['limit']),
            "productcategories" => self::getProductByProductCategories($product['id'], $product['limit']),
            "manufacturers" => self::getProductByManufacturer($product['id'], $product['limit'])
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
}
