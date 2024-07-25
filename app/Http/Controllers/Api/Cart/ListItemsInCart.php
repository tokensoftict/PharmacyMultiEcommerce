<?php

namespace App\Http\Controllers\Api\Cart;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Http\Resources\Api\Stock\StockInCartResource;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ListItemsInCart extends ApiController
{
    /**
     * @param AddItemRequest $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();
        $cart = $application->cart ?? [];

        $stocks = Stock::whereKey(array_keys($cart))->get();
        $totalItemsInCarts = 0;
        $stocks = $stocks->map(function($stock) use ($cart, &$totalItemsInCarts){
            $price = $stock->{ApplicationEnvironment::$stock_model_string}->price;
            $stock->cart_quantity = $cart[$stock->id]['quantity'];
            $stock->added_date = $cart[$stock->id]['date'];
            $stock->price = $price;
            $stock->total = ($cart[$stock->id]['quantity'] * $price);
            $totalItemsInCarts+= ($cart[$stock->id]['quantity'] * $price);
            return $stock;
        });

        return $this->sendSuccessResponse([
            "items" =>StockInCartResource::collection($stocks),
            "meta" => [
                "noItems" => $stocks->count(),
                "totalItemsInCarts" =>number_format($totalItemsInCarts, 2)
            ]
        ]);
    }

}
