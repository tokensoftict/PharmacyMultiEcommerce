<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\HomePageApiParser;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Models\Manufacturer;
use Illuminate\Support\Arr;

class SupermarketHomeControllerBankup extends ApiController
{
    public function __invoke()
    {
        $data = [];
        $components = [
            [
                "component" => "topBrands",
                "type" => "topBrands",
            ],
            [
                "component" => "ImageSlider",
                "type" => "ImageSlider",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 38,
                "limit" => 15,
                "label" => "DEODORANT",
                "seeAll" => "stock/38/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 12,
                "limit" => 15,
                "label" => "ANTIMALARIAL",
                "seeAll" => "stock/12/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 27,
                "limit" => 15,
                "label" => "HERBAL SUPPLEMENT",
                "seeAll" => "stock/27/by_classification"
            ],
            [
                "component" => "FlashDeals",
                "type" => "lowestClassifications",
                "label" => "LOWEST PRICE YOU CAN TRUST",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 31,
                "limit" => 15,
                "label" => "BABY SUPPLEMENT",
                "seeAll" => "stock/31/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 149,
                "limit" => 15,
                "label" => "SKIN SOAPS",
                "seeAll" => "stock/149/by_classification"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "manufacturers",
                "id" => 161,
                "limit" => 15,
                "label" => "TUYIL PHARMACEUTICAL STORE",
                "seeAll" => "stock/161/by_manufacturer"
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
