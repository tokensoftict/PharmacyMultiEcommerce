<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Brand;
use App\Models\Classification;
use App\Models\Country;
use App\Models\Manufacturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountriesController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return $this->sendSuccessResponse(
            Country::query()->select("id", "name")->orderBy("name", "ASC")->get()
        );
    }
}
