<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\HomePageApiParser;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Models\Manufacturer;
use Illuminate\Support\Arr;

class SupermarketHomeController extends ApiController
{
    public function __invoke()
    {
        $data = [];
        $components = [
            [
                "component" => "Horizontal_List",
                "type" => "topSellingProduct",
                "id" => 989,
                "limit" => 15,
                "label" => "BEST SELLERS",
                "seeAll" => "stock/bestseller",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "manufacturers",
                "id" => 114,
                "limit" => 15,
                "label" => "PEACE PHARMACEUTICAL STORE",
                "seeAll" => "stock/114/by_manufacturer"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 72,
                "limit" => 15,
                "label" => "SUPPLEMENTS",
                "seeAll" => "stock/72/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 93,
                "limit" => 15,
                "label" => "DRINKS",
                "seeAll" => "stock/93/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 71,
                "limit" => 15,
                "label" => "BODY CREAMS & LOTIONS",
                "seeAll" => "stock/71/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 148,
                "limit" => 15,
                "label" => "PERFUMES & BODY SPRAYS",
                "seeAll" => "stock/148/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 189,
                "limit" => 15,
                "label" => "BABY PRODUCTS",
                "seeAll" => "stock/189/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 105,
                "limit" => 15,
                "label" => "PROVISIONS & FOOD STUFF",
                "seeAll" => "stock/105/by_classification"
            ],
            [
                "component" => "FlashDeals",
                "type" => "lowestClassificationsRetail",
                "label" => "LOWEST PRICE YOU CAN TRUST",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 68,
                "limit" => 15,
                "label" => "BEVERAGES",
                "seeAll" => "stock/68/by_classification"
            ],
        ];


        foreach ($components as $component) {
            $data[] = array_merge($component, ["data" => HomePageApiParser::parseProductType($component)]);
        }

        //check for new Arrival
        $checkNewArrivals = [
            "component" => "Horizontal_List",
            "type" => "new_arrivals",
            "id" => "new-arrivals",
            "limit" => 15,
            "label" => "New Arrivals 🛍️",
            "seeAll" => "stock/new-arrivals"
        ];
        $NewArrivalData = HomePageApiParser::parseProductType($checkNewArrivals);
        if ($NewArrivalData->count() > 0) {
            $arrivalData = array_merge($checkNewArrivals, ["data" => $NewArrivalData]);
            $oneData = $data[2];
            $data[2] = $arrivalData;
            $data[] = $oneData;
        }

        $activePromos = \App\Models\Promotion::where('status_id', status('Approved'))
            ->where('app_id', 6) // Supermarket
            ->where('from_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['promotion_items.stock'])
            ->get();

        $carouselItems = [];
        foreach ($activePromos as $promo) {
            $firstImage = null;
            $firstItem = $promo->promotion_items->first();
            if ($firstItem && $firstItem->stock) {
                $firstImage = $firstItem->stock->product_image;
            }

            if (!$firstImage) {
                // Try to find any stock in the promo that has an image
                foreach ($promo->promotion_items as $item) {
                    if ($item->stock && $item->stock->product_image) {
                        $firstImage = $item->stock->product_image;
                        break;
                    }
                }
            }

            $carouselItems[] = [
                "promotionId" => $promo->id,
                "title" => $promo->name,
                "startDate" => $promo->from_date->toIso8601String(),
                "endDate" => $promo->end_date->toIso8601String(),
                "image" => $firstImage ?? asset("logo/no-image.png"),
            ];
        }

        if (count($carouselItems) > 0) {
            array_unshift($data, [
                "component" => "PromoCarousel",
                "type" => "PromoCarousel",
                "data" => $carouselItems,
            ]);
        }

        return $this->sendSuccessResponse(array_merge($promos ?? [], $data));
    }
}
