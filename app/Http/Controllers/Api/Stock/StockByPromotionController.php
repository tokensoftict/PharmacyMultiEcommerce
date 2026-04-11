<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Promotion;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StockByPromotionController extends ApiController
{
    /**
     * @param Request $request
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function __invoke(Request $request, Promotion $promotion) : JsonResponse
    {
        $stockIds = $promotion->promotion_items()->pluck('stock_id');
        
        $stocks = Stock::whereIn('id', $stockIds)
            ->where('status_id', 1)
            ->paginate();

        return $this->sendPaginatedSuccessResponse(
            StockListResource::collection($stocks)->response()->getData(true)
        );
    }
}
