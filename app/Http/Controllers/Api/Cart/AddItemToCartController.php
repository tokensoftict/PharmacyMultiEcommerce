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
        $application = getApplicationModel();
        if (!$application) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", 422);
        }

        $cart = $application->cart ?? null;
        if(is_null($cart)){
            $cart = [];
        }

        $stockId = $request->get("stock_id");
        $quantity = $request->get("quantity");
        $acceptDependent = $request->get("accept_dependent", false);

        $item = [
            "id" => $stockId,
            "quantity" => $quantity,
            "date" => now()->format("Y-m-d")
        ];

        if ($quantity > 0) {
            $cart[$stockId] = $item;
        } else {
            unset($cart[$stockId]);
        }

        // Handle Dependent Products
        $stock = \App\Models\Stock::find($stockId);
        if ($stock) {
            // Check if we should process dependents: 
            // 1. Explicitly accepted in this request
            // 2. OR already exists in cart as a dependent of this parent
            $shouldProcessDependents = $acceptDependent;
            
            if (!$shouldProcessDependents) {
                foreach ($cart as $id => $cartItem) {
                    if (isset($cartItem['is_dependent']) && isset($cartItem['parent_stock_id']) && $cartItem['parent_stock_id'] == $stockId) {
                        $shouldProcessDependents = true;
                        break;
                    }
                }
            }

            if ($shouldProcessDependents) {
                foreach ($stock->dependent_products as $dependent) {
                    $parentRatio = $dependent->parent ?: 1;
                    $childRatio = $dependent->child ?: 1;
                    $dependentQty = floor($quantity / $parentRatio) * $childRatio;
                    
                    // We need the internal ID of the dependent stock
                    $dependentStock = $dependent->dependent_stock;
                    
                    if ($dependentQty > 0 && $dependentStock) {
                        $cart[$dependentStock->id] = [
                            "id" => $dependentStock->id,
                            "quantity" => $dependentQty,
                            "date" => now()->format("Y-m-d"),
                            "is_dependent" => true,
                            "parent_stock_id" => $stockId
                        ];
                    } else if ($dependentStock) {
                        // Remove if ratio no longer met
                        unset($cart[$dependentStock->id]);
                    }
                }
            }
        }

        $application->cart = $cart;
        $application->update();

        return $this->sendSuccessResponse(['message' => "Item has been added to cart successfully"]);
    }
}
