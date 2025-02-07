<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Brand;
use App\Models\Classification;
use App\Models\Country;
use App\Models\LocalGovt;
use App\Models\Manufacturer;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalGovernmentController extends ApiController
{

    /**
     * @param State $state
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(State $state, Request $request) : JsonResponse
    {
        return $this->sendSuccessResponse(
            LocalGovt::query()->select("id", "name")->where("state_id", $state->id)->orderBy("name", "ASC")->get()
        );
    }
}
