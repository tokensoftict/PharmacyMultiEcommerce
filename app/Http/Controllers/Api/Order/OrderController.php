<?php

namespace app\Http\Controllers\Api\Order;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Http\Resources\Api\Order\OrderListResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class OrderController extends ApiController
{

    /**
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    public function __invoke(Request $request, Order $order) : JsonResponse
    {

        return $this->showOne(
            new OrderResource($order),
        );
    }

}
