<?php

namespace App\Http\Controllers\Api\Popup;

use App\Http\Controllers\ApiController;
use App\Models\SupermarketUser;
use App\Models\UserStockPromotion;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CustomerSpecialPromotionController extends ApiController
{

    public function __invoke() : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = [];
        $this->getUserSpecialDiscounts($checkoutUser, $data);

        return $this->sendSuccessResponse($data);
    }



    private function getUserSpecialDiscounts($user, array &$data) : void
    {
        if($user instanceof SupermarketUser) {
            $productPromotions = UserStockPromotion::query()
                ->where("user_id", $user->user_id)
                ->where('status_id', status('Approved'))
                ->get();
            foreach ($productPromotions as $productPromotion) {
                $data[] = [
                    "id" => count($data) + 1,
                    "type" => "discount",
                    "title" => "⏰ Flash Deal Alert!",
                    "message" => "For a limited time only, we’re giving you 2% off your order. Grab your favorites now before this special offer disappears!",
                    "cta" => "Shop Now",
                    "application" => "supermarket",
                    "promotion" => $productPromotion,
                    "link" => ""
                ];
            }
        }
    }
}
