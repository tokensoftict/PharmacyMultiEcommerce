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
                "component" => "topBrands",
                "type"       => "topBrands",
            ],
            [
                "component" => "ImageSlider",
                "type"       => "ImageSlider",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "manufacturers",
                "id" => 114,
                "limit" => 15,
                "label" => "Peace Pharmaceutical Store",
                "seeAll" => "stock/114/by_manufacturer"
            ],
            [
                "component" => "Horizontal_List",
                "type" => "classifications",
                "id" => 12,
                "limit" => 15,
                "label" => "Antimalarial",
                "seeAll" => "stock/12/by_classification"
            ],
            [
                "component" => "FlashDeals",
                "type"       => "lowestClassifications",
                "label"     => "Lowest Price - Items Must Go",
            ],
            [
                "component" => "Some",
                "type"       => "Text",
            ],
            [
                "component" => "Horizontal_List",
                "type" => "manufacturers",
                "id" => 161,
                "limit" => 15,
                "label" => "Tuyil Pharmaceutical Store",
                "seeAll" => "stock/114/by_manufacturer"
            ],
        ];


        foreach ($components as $component){
            $data[] = array_merge($component, [ "data" => HomePageApiParser::parseProductType($component)]);
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
        if($NewArrivalData->count() > 0) {
            $data[] = array_merge($checkNewArrivals, [ "data" => $NewArrivalData]);
        }

        return $this->sendSuccessResponse($data);
    }
}
