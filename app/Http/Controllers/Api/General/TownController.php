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
     * @param State $state
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(State $state, Request $request) : JsonResponse
    {
        $towns = Town::query()->where('name', '<>', 'Others')->select("id", "name")->where("state_id", $state->id)->orderBy("name", "ASC")->get();
        $towns->add([
            'id' => 152,
            'name' => 'Others',
            'state_id' => 4122,
            'local_govt_id' => 738,
        ]);
        return $this->sendSuccessResponse(
            $towns
        );
    }
}
