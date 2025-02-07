<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SaveDeliveryAddressController extends ApiController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function __invoke(Request $request)  : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $checkoutUser->saveCheckoutData("deliveryAddressId", $request->get("deliveryAddressId"));

        return $this->sendSuccessResponse([
            "address" => Address::find($request->get("deliveryAddressId"))
        ]);
    }

}
