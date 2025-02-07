<?php

namespace app\Http\Controllers\Api\General;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\DeliveryMethodResource;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetDeliveryMethodAsDefaultDeliveryMethodController extends ApiController
{
    /**
     * @param DeliveryMethod $deliveryMethod
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(DeliveryMethod $deliveryMethod, Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();

        $application?->setDefaultDeliveryMethod($deliveryMethod);

        return $this->showOne(
            new DeliveryMethodResource($deliveryMethod),
        );
    }
}
