<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\BestSellerStockResource;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BestSellerController extends ApiController
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
        return $this->sendPaginatedSuccessResponse(
            BestSellerStockResource::collection(
                $this->service->getBestSellers()
            )->response()->getData(true)
        );
    }
}
