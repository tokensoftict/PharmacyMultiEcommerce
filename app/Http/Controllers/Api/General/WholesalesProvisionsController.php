<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Classification;
use App\Models\OrderProduct;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Populates the Wholesale Provisions store with component-based structure.
 *
 * Eligibility Criteria:
 * 1. Stock admin_status = 1 (active)
 * 2. Stock classification.major_classification = SUPERMARKET and status = 1
 * 3. Stock has wholessales_stock_prices with status = 1
 */
class WholesalesProvisionsController extends ApiController
{
    private const MAJOR_CLASSIFICATION = 'SUPERMARKET';

    public function __invoke(Request $request)
    {
        $perPage = min(max((int)$request->query('per_page', 20), 1), 50);
        $category = $request->query('category'); // optional classification id filter

        // ---- Categories list ----
        $categories = Classification::query()
            ->where('major_classification', self::MAJOR_CLASSIFICATION)
            ->where('status', true)
            ->whereHas('stocks', fn($q) => $q
                ->where('admin_status', true)
                ->whereHas('wholessales_stock_prices', fn($sq) => $sq->where('status', true))
            )
            ->inRandomOrder()
            ->limit(10)
            ->get(['id', 'name', 'seo'])
            ->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name, 'seo' => $c->seo]);

        // ---- Popular picks: best selling provisions in wholesales ----
        $topIds = OrderProduct::query()
            ->where('app_id', ApplicationEnvironment::$model_id)
            ->whereIn('stock_id', $this->baseQuery()->select('stocks.id'))
            ->groupBy('stock_id')
            ->selectRaw('stock_id, SUM(quantity) as total_sold')
            ->orderByDesc('total_sold')
            ->limit(12)
            ->pluck('stock_id')
            ->toArray();

        $popular = [];
        if (count($topIds)) {
            $popular = $this->baseQuery()
                ->whereIn('stocks.id', $topIds)
                ->orderByRaw('FIELD(stocks.id, ' . implode(',', array_map('intval', $topIds)) . ')')
                ->get();
        }

        // ---- Deals: stock with special price ----
        $deals = $this->baseQuery()
            ->whereHas('wholessales_stock_prices', fn($q) => $q->where('special_offer', 1)->where('status', true))
            ->limit(12)
            ->get();

        // ---- All provisions (paginated) ----
        $all = $this->baseQuery()
            ->when($category, fn($q) => $q->where('stocks.classification_id', $category))
            ->orderBy('stocks.name')
            ->paginate($perPage);

        // ---- Build Component Structure (Backend Driven UI) ----
        $components = [
            [
                "component" => "CategoryChips",
                "type" => "categories",
                "data" => $categories,
            ],
        ];

        if (count($popular) > 0) {
            $components[] = [
                "component" => "Horizontal_List",
                "type" => "popular_picks",
                "label" => "Popular Wholesale Picks",
                "data" => StockListResource::collection($popular),
            ];
        }

        if (count($deals) > 0) {
            $components[] = [
                "component" => "FlashDeals",
                "type" => "deals",
                "label" => "Wholesale Deals",
                "data" => StockListResource::collection($deals),
            ];
        }

        if (count($categories) > 0) {
            $components[] = [
                "component" => "CategoryGrid",
                "type" => "shop_by_category",
                "label" => "Shop by Category",
                "data" => $categories,
            ];
        }

        $components[] = [
            "component" => "Grid_List",
            "type" => "all_products",
            "label" => "All Provisions",
            "data" => StockListResource::collection($all->getCollection()),
            "pagination" => [
                "current_page" => $all->currentPage(),
                "last_page" => $all->lastPage(),
                "total" => $all->total(),
                "has_more" => $all->hasMorePages(),
            ],
        ];

        return $this->sendSuccessResponse([
            'components' => $components,
            'popular_picks' => StockListResource::collection($popular),
            'deals' => StockListResource::collection($deals),
            'categories' => $categories,
            'all_products' => [
                'data' => StockListResource::collection($all->getCollection()),
                'current_page' => $all->currentPage(),
                'last_page' => $all->lastPage(),
                'total' => $all->total(),
                'has_more' => $all->hasMorePages(),
            ],
        ]);
    }

    /**
     * Base eligibility:
     * - Stock admin_status = true (1)
     * - Classification major_classification = SUPERMARKET and status = true (1)
     * - Wholesales price status = true (1)
     */
    private function baseQuery(): Builder
    {
        return Stock::query()
            ->where('admin_status', true)
            ->whereHas('classification', fn($q) => $q
                ->where('major_classification', self::MAJOR_CLASSIFICATION)
                ->where('status', true))
            ->whereHas('wholessales_stock_prices', fn($q) => $q->where('status', true));
    }
}
