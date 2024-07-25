<?php

namespace App\Http\Controllers\Api\Cart;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;


class AddItemToCartController extends ApiController
{

    /**
     * @param AddItemRequest $request
     * @return JsonResponse
     */
    public function __invoke(AddItemRequest $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();
        $cart = $application->cart ?? null;
        if(is_null($cart)){
            $cart = [];
        }

        $item = [
            "id" => $request->get("stock_id"),
            "quantity" => $request->get("quantity"),
            "date" => now()->format("Y-m-d")
        ];

        $cart[$request->get("stock_id")] = $item;

        $application->cart = $cart;

        $application->update();


        return $this->sendSuccessResponse(['message' => "Item has been added to cart successfully"]);
    }

}
