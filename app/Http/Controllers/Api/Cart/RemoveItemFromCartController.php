<?php

namespace App\Http\Controllers\Api\Cart;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RemoveItemFromCartController extends ApiController
{

    /**
     * @param Stock $stock
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Stock $stock, Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();
        $cart = $application->cart;

        if(is_null($cart)) return $this->sendSuccessMessageResponse("Item has been removed successfully");

        Arr::forget($cart, $stock->id);

        $application->cart = $cart;

        $application->update();

        return $this->sendSuccessMessageResponse("Item has been removed successfully");
    }
}
