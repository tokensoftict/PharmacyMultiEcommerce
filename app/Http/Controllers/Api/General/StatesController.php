<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Brand;
use App\Models\Classification;
use App\Models\Country;
use App\Models\Manufacturer;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatesController extends ApiController
{

    /**
     * @param Country $country
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Country $country, Request $request) : JsonResponse
    {
        return $this->sendSuccessResponse(
            State::query()->where('name', '<>', 'Others')->select("id", "name")->where("country_id", $country->id)->orderBy("name", "ASC")->get()
        );
    }
}
