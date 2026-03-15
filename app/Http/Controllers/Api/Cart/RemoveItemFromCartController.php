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
        $application = getApplicationModel();
        if (!$application) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", 422);
        }
        $cart = $application->cart;

        if(is_null($cart)) return $this->sendSuccessMessageResponse("Item has been removed successfully");

        $stockId = $stock->id;
        Arr::forget($cart, $stockId);

        // Remove linked dependent products
        foreach ($cart as $id => $item) {
            if (isset($item['is_dependent']) && isset($item['parent_stock_id']) && $item['parent_stock_id'] == $stockId) {
                Arr::forget($cart, $id);
            }
        }

        $application->cart = $cart;

        $application->update();

        return $this->sendSuccessMessageResponse("Item has been removed successfully");
    }
}
