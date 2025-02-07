<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Wishlist\AddItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;


class AddItemToWishlistController extends ApiController
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
        $wishlist = $application->wishlist ?? null;
        if(is_null($wishlist)){
            $wishlist = [];
        }

        $item = [
            "id" => $request->get("stock_id"),
            "date" => now()->format("Y-m-d")
        ];

        $wishlist[$request->get("stock_id")] = $item;

        $application->wishlist = $wishlist;

        $application->update();

        return $this->sendSuccessResponse(['message' => "Item has been added to wishlist successfully"]);
    }

}
