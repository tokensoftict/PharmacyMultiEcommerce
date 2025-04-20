<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\HomePageApiParser;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Models\Manufacturer;
use Illuminate\Support\Arr;

class WholesalesHomeController extends ApiController
{
    public function __invoke()
    {
        $data = [
            "topBrands" => [5, 7, 20, 4, 6, 8, 3, 7, 5],

            "banners" => [
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://store.mophethpharmacy.com/wp-content/uploads/2022/10/Cough-Cold-and-flu.png",
                    "label" => "Cough Code & Flu",
                ],
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://store.mophethpharmacy.com/wp-content/uploads/2022/10/First-Aid.png",
                    "label" => "First Aid",
                ],
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://store.mophethpharmacy.com/wp-content/uploads/2022/10/Medicinal-Teas.png",
                    "label" => "Medicinal Teas",
                ],
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://store.mophethpharmacy.com/wp-content/uploads/2022/10/Multi-vitamins.png",
                    "label" => "Multi Vitamins",
                ],
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://store.mophethpharmacy.com/wp-content/uploads/2022/10/Supplement.png",
                    "label" => "Supplement",
                ],
                [
                    "ref" => "productcategories",
                    "id" => 10,
                    "link" =>
                        "https://healthplusnigeria.com/cdn/shop/collections/pain-management-healthplus.jpg",
                    "label" => "Pain Management",
                ],
            ],

            "productLists" => [
                [
                    "type" => "productcategories",
                    "id" => 5,
                    "limit" => 15,
                    "label" => "Official Peace Store",
                ],
                [
                    "type" => "classifications",
                    "id" => 7,
                    "limit" => 15,
                    "label" => "Pain Reliefs",
                ],
            ],
        ];

        $manufacturers = Manufacturer::whereIn("id", $data['topBrands'])->get();
        $manufacturers = GeneralResource::collection($manufacturers);
        Arr::set($data, "topBrands", $manufacturers);
        foreach ($data['productLists'] as $key=>$product){
            $data['productLists'][$key]['products'] = HomePageApiParser::parseProductType($product);
        }
        return $this->sendSuccessResponse($data);
    }
}
