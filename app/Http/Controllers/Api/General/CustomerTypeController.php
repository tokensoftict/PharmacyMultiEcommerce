<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Models\CustomerType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerTypeController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return $this->sendSuccessResponse(
            CustomerType::query()->where('status', 1)->select("id", "name")->orderBy("name", "ASC")->get()
        );
    }
}
