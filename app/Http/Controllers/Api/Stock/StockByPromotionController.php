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
            ->orderBy(ApplicationEnvironment::$stock_model_string . ".price", "asc")->paginate(config("app.PAGINATE_NUMBER"));

        return $this->sendPaginatedSuccessResponse(
            StockListResource::collection($stocks)->response()->getData(true)
        );
    }
}
