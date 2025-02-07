<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PaymentMethodListController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public  function __invoke(Request $request)  : JsonResponse
    {
        $checkOutUser = getApplicationModel();

        if(!$checkOutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $selectedDeliveryMethod = $checkOutUser->getCheckoutDeliveryMethod();

        if(!$selectedDeliveryMethod) return $this->sendErrorResponse("Please select your preferred delivery method!", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $code = DeliveryMethod::findorfail($selectedDeliveryMethod['extraData']['deliveryMethod'])->code;

        $availablePaymentMethodCode = getAvailablePaymentOption($code);

        $paymentMethods = PaymentMethod::whereIn('code', $availablePaymentMethodCode)->where("app_id", ApplicationEnvironment::$id)->get();
        return $this->showAll($paymentMethods);
    }

}
