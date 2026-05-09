<?php

namespace App\Http\Controllers\Api\Popup;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockShowResource;
use App\Models\SupermarketUser;
use App\Models\UserStockPromotion;
use App\Classes\Settings;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CustomerSpecialPromotionController extends ApiController
{

    public function __invoke(): JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if (!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = [];
        $this->getUserSpecialDiscounts($checkoutUser, $data);

        return $this->sendSuccessResponse($data);
    }



    private function getUserSpecialDiscounts($user, array &$data): void
    {
        if ($user instanceof SupermarketUser) {
            $productPromotions = UserStockPromotion::query()
                ->where("user_id", $user->user_id)
                ->where('status_id', status('Approved'))
                ->where('end_date', ">=", now())
                ->get();

            foreach ($productPromotions as $productPromotion) {
                $discount = $productPromotion->discount_percentage ?: app(Settings::class)->get('discount_percentage');

                $promoTest = [
                    [
                        "title" => "💊 Running Low? Refill & Save {$discount}%!",
                        "message" => "Your medication is almost finished. Refill today and enjoy an exclusive {$discount}% discount before you run out."
                    ],
                    [
                        "title" => "⏳ Don’t Miss a Dose!",
                        "message" => "Only ~30 doses left! Stay consistent with your treatment. Refill now and get {$discount}% off your next order."
                    ],
                    [
                        "title" => "🎁 Special Refill Reward",
                        "message" => "Because you’re staying on track with your dosage, we’re giving you a {$discount}% discount to refill your medication today."
                    ],
                    [
                        "title" => "🚑 Refill Reminder: Save {$discount}%!",
                        "message" => "Your prescription is running low. Refill in advance and enjoy a {$discount}% savings—don’t wait until it’s too late."
                    ],
                    [
                        "title" => "🌟 Exclusive Discount Just for you",
                        "message" => "You’re close to finishing your current supply. Get ahead of schedule and refill with a {$discount}% discount—just for you."
                    ],
                ];

                $message = $promoTest[array_rand($promoTest)];

                $data[] = [
                    "id" => count($data) + 1,
                    "type" => "discount",
                    "title" => $message['title'],
                    "message" => $message['message'],
                    "cta" => "Shop Now",
                    "image" => $productPromotion->stock->product_image,
                    "application" => "supermarket",
                    "promotion" => $productPromotion,
                    "stock" => new StockShowResource($productPromotion->stock),
                    "show" => false,
                    "link" => [
                        "page" => "detailProduct",
                        "extraData" => [
                            "productId" => $productPromotion->stock_id,
                        ]
                    ]
                ];
            }
        }
    }
}
