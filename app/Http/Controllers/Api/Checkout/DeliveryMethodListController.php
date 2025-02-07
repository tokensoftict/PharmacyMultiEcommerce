<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryMethodListController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public  function __invoke(Request $request)  : JsonResponse
    {
        $deliveryMethods = DeliveryMethod::where("app_id", ApplicationEnvironment::$id)->get();
        return $this->showAll($deliveryMethods);
    }

}
