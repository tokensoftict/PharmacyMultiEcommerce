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
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(State $state, Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            GeneralResource::collection(
                LocalGovt::query()->select("id", "name")->where("state_id", $state->id)->orderBy("name", "ASC")->paginate(config("app.PAGINATE_NUMBER"))
            )->response()->getData(true)
        );
    }
}
