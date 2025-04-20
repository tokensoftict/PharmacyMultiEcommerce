<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Models\Classification;
use App\Models\NewStockArrival;
use App\Models\Stock;
use App\Services\Kafka\ProcessGeneralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NewArrivalPushController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {

        ProcessGeneralService::newArrival($request->get("data"), $request->get("store"));
        return $this->sendSuccessMessageResponse("New arrival as been saved successfully");
    }
}
