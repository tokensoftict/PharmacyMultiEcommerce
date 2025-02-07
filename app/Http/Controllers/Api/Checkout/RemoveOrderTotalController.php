<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class RemoveOrderTotalController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $checkoutUser->saveOrderTotalToRemoveOrAdd($request->get('order_total_id'));

        return $this->sendSuccessMessageResponse("Order total has been removed!");
    }
}
