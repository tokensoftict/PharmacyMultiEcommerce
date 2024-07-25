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
use App\Models\Town;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TownController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(LocalGovt $localGovt, Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            GeneralResource::collection(
                Town::query()->select("id", "name")->where("local_govt_id", $localGovt->id)->orderBy("name", "ASC")->paginate(config("app.PAGINATE_NUMBER"))
            )->response()->getData(true)
        );
    }
}
