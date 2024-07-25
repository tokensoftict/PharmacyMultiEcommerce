<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Brand;
use App\Models\Classification;
use App\Models\Manufacturer;
use App\Models\Productgroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductGroupController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            GeneralResource::collection(
                Productgroup::query()->select("id", "name")->where("status", 1)->orderBy("name", "ASC")->paginate(config("app.PAGINATE_NUMBER"))
            )->response()->getData(true)
        );
    }
}
