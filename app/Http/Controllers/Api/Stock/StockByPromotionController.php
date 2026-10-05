<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Promotion;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Classes\ApplicationEnvironment;


class StockByPromotionController extends ApiController
{
    /**
     * @param Request $request
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function __invoke(Request $request, Promotion $promotion): JsonResponse
    {
        $stockIds = $promotion->promotion_items()->pluck('stock_id');

        $stocks = Stock::query()->select("stocks.*", ApplicationEnvironment::$stock_model_string . ".price", ApplicationEnvironment::$stock_model_string . ".quantity as quantity", ApplicationEnvironment::$stock_model_string . ".expiry_date as expiry_date")->withoutGlobalScope('filter_stocks')
            ->join(ApplicationEnvironment::$stock_model_string, ApplicationEnvironment::$stock_model_string . ".stock_id", "=", "stocks.id")
            ->whereIn("stocks.id", $stockIds)
            ->when($request->input('search', $request->input('query')), function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where("stocks.name", "LIKE", "%" . $search . "%")
                        ->orWhere("stocks.description", "LIKE", "%" . $search . "%")
                        ->orWhere("stocks.seo", "LIKE", "%" . $search . "%");
                });
            })
            ->when($request->input('sort'), function ($q, $sort) {
                $price = ApplicationEnvironment::$stock_model_string . ".price";
                match ($sort) {
                    'price_asc' => $q->orderBy($price, 'asc'),
                    'price_desc' => $q->orderBy($price, 'desc'),
                    'name_asc' => $q->orderBy('stocks.name', 'asc'),
                    'newest' => $q->orderBy('stocks.id', 'desc'),
                    default => null,
                };
            })
            ->orderBy(ApplicationEnvironment::$stock_model_string . ".price", "asc")->paginate(config("app.PAGINATE_NUMBER"));

        return $this->sendPaginatedSuccessResponse(
            StockListResource::collection($stocks)->response()->getData(true)
        );
    }
}
