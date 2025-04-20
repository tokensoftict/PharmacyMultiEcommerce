<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Manufacturer;
use App\Models\Productcategory;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StockByProductManufacturerController extends ApiController
{
    public StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Manufacturer $manufacturer
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Manufacturer $manufacturer, Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            StockListResource::collection(
                $this->service->getByManufacturer($manufacturer)
            )->response()->getData(true)
        );
    }
}
