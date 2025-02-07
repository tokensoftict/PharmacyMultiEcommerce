<?php

namespace app\Http\Controllers\Api\Order;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Http\Resources\Api\Order\OrderListResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class OrdersController extends ApiController
{
    /**
     * @param AddItemRequest $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();
        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $orders = Order::query()
            ->where("customer_type", get_class($checkoutUser))
            ->where('customer_id', $checkoutUser->id)
            ->orderBy("id", "desc")
            ->get();


        return $this->sendSuccessResponse(
           OrderListResource::collection($orders)
        );
    }

}
