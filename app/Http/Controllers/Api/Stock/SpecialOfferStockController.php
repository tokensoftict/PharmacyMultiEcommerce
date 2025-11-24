<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListJoinResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Http\Resources\Api\Stock\StockNewArrivalListResource;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SpecialOfferStockController extends ApiController
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
            StockListJoinResource::collection(
                $this->service->getSpecialOffers()
            )->response()->getData(true)
        );
    }
}
