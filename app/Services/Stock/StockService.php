<?php

namespace App\Services\Stock;

use App\Classes\ApplicationEnvironment;
use App\Http\Resources\Api\Stock\StockListResource;
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
    public final function getBestSellers(?string $search = null): LengthAwarePaginator
    {
        $bestSellingProduct = OrderProduct::query()->select("stock_id")
            ->where('app_id', ApplicationEnvironment::$model_id)
            ->groupBy('stock_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(60)
            ->pluck("stock_id")
            ->toArray();

        $builder = Stock::query()->whereIn("id", $bestSellingProduct);
        $this->applySearch($builder, $search);

        return $builder->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @param int $manufacturer_id
     * @return LengthAwarePaginator
     */
    public final function getByManufacturer(Manufacturer|int $manufacturer, ?string $search = null): LengthAwarePaginator
    {
        if (!$manufacturer instanceof Manufacturer) {
            $manufacturer = Manufacturer::findOrFail($manufacturer);
        }
        return Stock::query()->select("stocks.*", ApplicationEnvironment::$stock_model_string . ".price", ApplicationEnvironment::$stock_model_string . ".quantity as quantity", ApplicationEnvironment::$stock_model_string . ".expiry_date as expiry_date")->withoutGlobalScope('filter_stocks')
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string . ".stock_id", "=", "stocks.id")
            ->where("manufacturer_id", $manufacturer->id)
            ->when($search, fn($q) => $this->applySearch($q, $search, "stocks."))
            ->orderBy(ApplicationEnvironment::$stock_model_string . ".quantity", "desc")
            ->paginate(config("app.PAGINATE_NUMBER"));

    }

    /**
     * @param Productcategory|int $productcategory
     * @return LengthAwarePaginator
     */
    public final function getByProductCategories(Productcategory|int $productcategory, ?string $search = null): LengthAwarePaginator
    {
        if (!$productcategory instanceof Productcategory) {
            $productcategory = Productcategory::findOrFail($productcategory);
        }
        return Stock::query()->select("stocks.*", ApplicationEnvironment::$stock_model_string . ".price", ApplicationEnvironment::$stock_model_string . ".quantity as quantity", ApplicationEnvironment::$stock_model_string . ".expiry_date as expiry_date")->withoutGlobalScope('filter_stocks')
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string . ".stock_id", "=", "stocks.id")
            ->where("productcategory_id", $productcategory->id)
            ->when($search, fn($q) => $this->applySearch($q, $search, "stocks."))
            ->orderBy(ApplicationEnvironment::$stock_model_string . ".quantity", "desc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }

    /**
     * @param Classification|int $classification
     * @return LengthAwarePaginator
     */
    public final function getByClassifications(Classification|int $classification, ?string $search = null): LengthAwarePaginator
    {
        if (!$classification instanceof Classification) {
            $classification = Classification::findOrFail($classification);
        }
        return Stock::query()->select("stocks.*", ApplicationEnvironment::$stock_model_string . ".price", ApplicationEnvironment::$stock_model_string . ".quantity as quantity", ApplicationEnvironment::$stock_model_string . ".expiry_date as expiry_date")->withoutGlobalScope('filter_stocks')
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string . ".stock_id", "=", "stocks.id")
            ->where("classification_id", $classification->id)
            ->when($search, fn($q) => $this->applySearch($q, $search, "stocks."))
            ->orderBy(ApplicationEnvironment::$stock_model_string . ".quantity", "desc")
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getFeaturedStock(): LengthAwarePaginator
    {
        return Stock::query()
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getSpecialOffers(?string $search = null): LengthAwarePaginator
    {
        return ApplicationEnvironment::$stock_model::query()->with('stock')
            ->where('special_offer', 1)
            ->when($search, fn($q) => $q->whereHas('stock', fn($sq) => $this->applySearch($sq, $search)))
            ->orderBy("price")
            ->paginate(config("app.PAGINATE_NUMBER"));

    }


    /**
     * @param Stock|int $stock
     * @return Stock
     */
    public final function getStock(Stock|int $stock): Stock
    {
        if (!$stock instanceof Stock) {
            $stock->findOrFail($stock);
        }

        return $stock;
    }

    /**
     * @return LengthAwarePaginator
     */
    public final function getPromotionalStock(?string $search = null): LengthAwarePaginator
    {
        return PromotionItem::query()->where("status_id", status("Approved"))->with(['stock'])
            ->when($search, fn($q) => $q->whereHas('stock', fn($sq) => $this->applySearch($sq, $search)))
            ->paginate(config("app.PAGINATE_NUMBER"));
    }


    /**
     * @return LengthAwarePaginator
     */
    public final function getNewArrivalsStock(?string $search = null): LengthAwarePaginator
    {
        $latestArrivals = NewStockArrival::selectRaw('MAX(id) as id')
            ->where('app_id', ApplicationEnvironment::$id)
            ->groupBy('stock_id')->pluck('id');

        return NewStockArrival::whereIn('id', $latestArrivals)
            ->with('stock')
            ->where('app_id', ApplicationEnvironment::$id)
            ->when($search, fn($q) => $q->whereHas('stock', fn($sq) => $this->applySearch($sq, $search)))
            ->whereHas('stock.' . ApplicationEnvironment::$stock_model_string, function ($query) {
                $query->where("quantity", ">", 0);
            })
            ->orderBy('id', 'DESC')
            ->paginate(config("app.PAGINATE_NUMBER"));
    }

    /**
     * @param string $query
     * @param string|null $storeType
     * @return LengthAwarePaginator
     */
    public final function search(string $query, string $storeType = null): LengthAwarePaginator
    {
        $name = explode(" ", $query);

        $builder = Stock::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('description', 'LIKE', '%' . $query . '%')
                    ->orWhere('seo', 'LIKE', '%' . $query . '%');
            })
            ->where('admin_status', true);

        if ($storeType) {
            $builder->where('store_type', $storeType);
        }

        if (ApplicationEnvironment::$stock_model_string === "wholessales_stock_prices") {
            $builder->where('is_wholesales', true);
        }

        return $builder->whereHas(ApplicationEnvironment::$stock_model_string, function ($q) {
            $q->where("status", true);
        })->paginate(config("app.PAGINATE_NUMBER"));
    }

    /**
     * Apply a free-text search (name, description, seo) to a stock query.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $builder
     * @param string|null $search
     * @param string $prefix column prefix, e.g. "stocks."
     * @return void
     */
    public final function applySearch($builder, ?string $search, string $prefix = ''): void
    {
        $search = trim((string)$search);
        if ($search === '') {
            return;
        }

        $builder->where(function ($q) use ($search, $prefix) {
            $q->where($prefix . 'name', 'LIKE', '%' . $search . '%')
                ->orWhere($prefix . 'description', 'LIKE', '%' . $search . '%')
                ->orWhere($prefix . 'seo', 'LIKE', '%' . $search . '%');
        });
    }

}
