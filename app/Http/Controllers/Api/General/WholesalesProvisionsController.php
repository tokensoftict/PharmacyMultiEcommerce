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
 * Populates the Wholesale Provisions store.
 *
 * A product qualifies only when its classification.major_classification is SUPERMARKET
 * AND it has a row in wholessales_stock_prices.
 */
class WholesalesProvisionsController extends ApiController
{
    private const MAJOR_CLASSIFICATION = 'SUPERMARKET';

    public function __invoke(Request $request)
    {
        $perPage = min(max((int)$request->query('per_page', 20), 1), 50);
        $category = $request->query('category'); // optional classification id filter

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
            ->whereHas('wholessales_stock_prices', fn($q) => $q->where('special_offer', 1))
            ->limit(12)
            ->get();

        // ---- Shop by category: random SUPERMARKET classifications ----
        $categories = Classification::query()
            ->where('major_classification', self::MAJOR_CLASSIFICATION)
            ->where('status', true)
            ->whereHas('stocks', fn($q) => $q->whereHas('wholessales_stock_prices'))
            ->inRandomOrder()
            ->limit(8)
            ->get(['id', 'name', 'seo'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'seo' => $c->seo]);

        // ---- All provisions (paginated) ----
        $all = $this->baseQuery()
            ->when($category, fn($q) => $q->where('stocks.classification_id', $category))
            ->orderBy('stocks.name')
            ->paginate($perPage);

        return $this->sendSuccessResponse([
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
     * Base eligibility: SUPERMARKET classification + has wholesales price.
     */
    private function baseQuery(): Builder
    {
        return Stock::query()
            ->whereHas('classification', fn($q) => $q
                ->where('major_classification', self::MAJOR_CLASSIFICATION)
                ->where('status', true))
            ->whereHas('wholessales_stock_prices');
    }
}
