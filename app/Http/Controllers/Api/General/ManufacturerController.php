<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Manufacturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManufacturerController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return $this->sendSuccessResponse(
            GeneralResource::collection(
                Manufacturer::query()->select("id", "name")->where("status", 1)->orderBy("name", "ASC")->get()
            )
        );
    }
}
