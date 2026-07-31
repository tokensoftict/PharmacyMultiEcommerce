<?php

namespace App\Services\Share;

use App\Http\ViewModels\ProductShareViewModel;
use App\Models\Stock;
use Illuminate\Support\Facades\Cache;

class ProductShareService
{
    /**
     * Cache TTL in seconds — 1 hour.
     * Social crawlers often re-scrape the same URL within minutes; caching
     * prevents a DB round-trip on every request.
     */
    private const CACHE_TTL = 3600;

    /**
     * Resolve a product by its SEO slug for the share page, including only
     * the relations and columns required to build the ViewModel.
     *
     * Returns null when the product does not exist or is disabled.
     *
     * @param string $slug      The SEO slug stored in stocks.seo
     * @param string $storeType Either 'retail' or 'wholesales'
     * @param string $currentUrl The full canonical URL of the current request
     */
    public function resolve(string $slug, string $storeType, string $currentUrl): ProductShareViewModel|null
    {
        $cacheKey = "product.share.{$storeType}.{$slug}";

        /** @var Stock|null $stock */
        $stock = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $storeType) {
            return $this->fetchStock($slug, $storeType);
        });

        if (! $stock) {
            return null;
        }

        return new ProductShareViewModel($stock, $storeType, $currentUrl);
    }

    /**
     * Perform the actual database query.
     *
     * We call withoutGlobalScopes() because StockModelTrait adds a global scope
     * that joins price tables filtered by ApplicationEnvironment (a per-request
     * middleware concern). Share pages are public and should not depend on that.
     *
     * We call without() to strip the 7 default eager-loads defined on the model
     * and replace them with only the 3 relations we actually need, keeping the
     * query lean for high-traffic crawler traffic.
     */
    private function fetchStock(string $slug, string $storeType): Stock|null
    {
        $priceRelation = $storeType === 'wholesales'
            ? 'wholessales_stock_prices'
            : 'supermarkets_stock_prices';

        $stock = Stock::withoutGlobalScopes()
            ->without(array_keys((new Stock())->getRelations()))  // strip default with()
            ->with([
                'manufacturer:id,name',
                'productcategory:id,name',
                'stock_media.media',
                $priceRelation,
            ])
            ->select([
                'id', 'local_stock_id', 'name', 'seo',
                'description', 'admin_status',
                'manufacturer_id', 'productcategory_id',
            ])
            ->where('seo', $slug)
            ->where('admin_status', true)
            ->first();

        return $stock;
    }

    /**
     * Flush the cache for a specific product slug and store type.
     * Call this from the Kafka consumer after a stock update.
     */
    public static function forgetCache(string $slug, string $storeType = null): void
    {
        if ($storeType) {
            Cache::forget("product.share.{$storeType}.{$slug}");
        } else {
            Cache::forget("product.share.retail.{$slug}");
            Cache::forget("product.share.wholesales.{$slug}");
        }
    }
}
