<?php

namespace app\Http\Controllers\Api\General;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\DeliveryMethodResource;
use App\Http\Resources\Api\General\PaymentMethodResource;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetPaymentMethodAsDefaultPaymentMethodController extends ApiController
{
    /**
     * @param PaymentMethod $paymentMethod
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(PaymentMethod $paymentMethod, Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();

        $application?->setDefaultPaymentMethod($paymentMethod);

        return $this->showOne(
            new PaymentMethodResource($paymentMethod),
        );
    }
}
