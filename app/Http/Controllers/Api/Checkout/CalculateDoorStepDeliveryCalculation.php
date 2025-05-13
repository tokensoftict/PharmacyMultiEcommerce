<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Models\DeliveryMethod;
use App\Repositories\DsdRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CalculateDoorStepDeliveryCalculation extends ApiController
{
    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $methodOfDelivery = DeliveryMethod::findorfail(7);

        $doorStepDeliveryAmount = (new DsdRepository())->getDsdAnalysis($checkoutUser->cart,  $methodOfDelivery, []);

        return $this->sendSuccessResponse($doorStepDeliveryAmount);

    }
}
