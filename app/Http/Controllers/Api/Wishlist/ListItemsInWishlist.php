<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Cart\AddItemRequest;
use App\Http\Resources\Api\Stock\StockInCartResource;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ListItemsInWishlist extends ApiController
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

        return $this->sendSuccessResponse(
            $checkoutUser->getWishlistItems()
        );
    }

}
