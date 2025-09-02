<?php

namespace App\Http\Controllers\Api\Stock;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockListJoinResource;
use App\Models\Productcategory;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StockByProductCategoriesController extends ApiController
{
    public StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Productcategory $productcategory
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Productcategory $productcategory, Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            StockListJoinResource::collection(
                $this->service->getByProductCategories($productcategory)
            )->response()->getData(true)
        );
    }
}
