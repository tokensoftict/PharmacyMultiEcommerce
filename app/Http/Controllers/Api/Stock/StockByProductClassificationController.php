<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Classification;
use App\Models\Manufacturer;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StockByProductClassificationController extends ApiController
{
    public StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Classification $classification
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Classification $classification, Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            StockListResource::collection(
                $this->service->getByClassifications($classification)
            )->response()->getData(true)
        );
    }
}
