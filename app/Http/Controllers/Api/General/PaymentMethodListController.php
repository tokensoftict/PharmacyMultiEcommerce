<?php

namespace app\Http\Controllers\Api\General;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodListController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $paymentMethods = PaymentMethod::query()->where("app_id", ApplicationEnvironment::$id)->where("status", "1")->get();
        return $this->sendSuccessResponse(
            PaymentMethodResource::collection($paymentMethods)
        );
    }
}
