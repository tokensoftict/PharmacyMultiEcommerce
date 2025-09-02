<?php

namespace App\Services\Stock;

use App\Classes\ApplicationEnvironment;
use App\Models\Classification;
use App\Models\Manufacturer;
use App\Models\NewStockArrival;
use App\Models\OrderProduct;
use App\Models\Productcategory;
use App\Models\PromotionItem;
use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;

class StockService
{
    /**
     * @return LengthAwarePaginator
     */
    public final function getBestSellers() : LengthAwarePaginator
    {
        return OrderProduct::query()->with(['stock', 'stock.'.ApplicationEnvironment::$stock_model_string])
            ->whereHas('order', function ($query) {
                $query->where('customer_type', get_class(ApplicationEnvironment::getApplicationRelatedModel()));
            })
            ->select("stock_id")
            ->groupBy('stock_id')
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @param int $manufacturer_id
     * @return LengthAwarePaginator
     */
    public final function getByManufacturer(Manufacturer|int $manufacturer) : LengthAwarePaginator
    {
        if(! $manufacturer instanceof Manufacturer){
            $manufacturer = Manufacturer::findOrFail($manufacturer);
        }
       return  Stock::where("manufacturer_id", $manufacturer->id)
           ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
           ->orderBy(ApplicationEnvironment::$stock_model_string.".price", "asc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }

    /**
     * @param Productcategory|int $productcategory
     * @return LengthAwarePaginator
     */
    public final function getByProductCategories(Productcategory|int $productcategory) : LengthAwarePaginator
    {
        if(! $productcategory instanceof Productcategory){
            $productcategory = Productcategory::findOrFail($productcategory);
        }
        return  Stock::where("productcategory_id", $productcategory->id)
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
            ->orderBy(ApplicationEnvironment::$stock_model_string.".price", "asc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }

    /**
     * @param Classification|int $classification
     * @return LengthAwarePaginator
     */
    public final function getByClassifications(Classification|int $classification) : LengthAwarePaginator
    {
        if(! $classification instanceof Classification){
            $classification = Classification::findOrFail($classification);
        }
        Stock::where("classification_id", $classification->id)
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
            ->orderByRaw("CAST(".ApplicationEnvironment::$stock_model_string.".price AS DECIMAL(8,2)) ASC")->dd();
        return  Stock::where("classification_id", $classification->id)
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
            ->orderByRaw("CAST(".ApplicationEnvironment::$stock_model_string.".price AS DECIMAL(8,2)) ASC")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getFeaturedStock() : LengthAwarePaginator
    {
        return Stock::query()
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
            ->orderBy(ApplicationEnvironment::$stock_model_string.".price", "asc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getSpecialOffers() : LengthAwarePaginator
    {
        return Stock::query()
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string.".stock_id", "=", "stocks.id")
            ->orderBy(ApplicationEnvironment::$stock_model_string.".price", "asc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @param Stock|int $stock
     * @return Stock
     */
    public final function getStock(Stock|int $stock) : Stock
    {
        if(! $stock instanceof Stock){
            $stock->findOrFail($stock);
        }

        return $stock;
    }

    /**
     * @return LengthAwarePaginator
     */
    public final function getPromotionalStock() : LengthAwarePaginator
    {
        return PromotionItem::query()->where("status_id", status("Approved"))->with(['stock'])->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getNewArrivalsStock() : LengthAwarePaginator
    {
        $latestArrivals = NewStockArrival::selectRaw('MAX(id) as id')
            ->groupBy('stock_id');

        return NewStockArrival::whereIn('id', $latestArrivals)
            ->with('stock')
            ->orderBy('id', 'DESC')
            ->paginate(config("app.PAGINATE_NUMBER"));
    }

}
