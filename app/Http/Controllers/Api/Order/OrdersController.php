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

    public array $orderTypes = [];

    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();
        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->orderTypes = [
            'In Progress' => [status("Pending"), status("Processing"), status("Packing"), status("Waiting For Payment"), status("Paid")],
            'Completed' => [status("Dispatched"), status("Completed")],
            'Cancelled' => [status("Cancelled")],
        ];

        $orders = Order::query()
            ->where("customer_type", get_class($checkoutUser))
            ->where('customer_id', $checkoutUser->id)
            ->whereIn("status_id", $this->orderTypes[$request->get('orderType')])
            ->orderBy("id", "desc")
            ->get();


        return $this->sendSuccessResponse(
            OrderListResource::collection($orders)
        );
    }

}
