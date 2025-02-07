<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Models\DeliveryMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SaveDeliveryMethodController extends ApiController
{

    public function __invoke(Request $request)  : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $checkoutUser->saveCheckoutData("deliveryMethod", [
            'deliveryMethod' => $request->get('deliveryMethod'),
            'extraData' => $request->post()
        ]);

        return $this->sendSuccessResponse([
            "method" => DeliveryMethod::find($request->get("deliveryMethod"))
        ]);
    }

}
