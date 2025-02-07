<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RemoveItemFromWishlistController extends ApiController
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
        $wishlist = $application->wishlist;

        if(is_null($wishlist)) return $this->sendSuccessMessageResponse("Item has been removed successfully");

        Arr::forget($wishlist, $stock->id);

        $application->wishlist = $wishlist;

        $application->update();

        return $this->sendSuccessMessageResponse("Item has been removed successfully");
    }
}
