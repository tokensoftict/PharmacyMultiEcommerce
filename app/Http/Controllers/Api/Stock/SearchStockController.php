<?php

namespace App\Http\Controllers\Api\Stock;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\SearchStockResource;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchStockController extends ApiController
{
    public StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $query = $request->get('query');
        $storeType = $request->get('store_type');

        if (!$query) {
            return $this->errorResponse("Search query is required", 400);
        }

        return $this->sendPaginatedSuccessResponse(
            SearchStockResource::collection($this->service->search($query, $storeType))
                ->response()->getData(true)
        );
    }
}
