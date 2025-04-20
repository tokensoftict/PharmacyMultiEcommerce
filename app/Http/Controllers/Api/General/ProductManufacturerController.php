<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Manufacturer;
use App\Models\Productcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductManufacturerController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return $this->sendPaginatedSuccessResponse(
            GeneralResource::collection(
                Manufacturer::query()->with([
                    'stocks' => fn ($query) => $query->limit(3)
                ])
                    ->has('stocks', '>', 2)
                    ->select("id", "name")->where("status", 1)
                    ->orderBy("name", "ASC")
                    ->paginate(config('app.PAGINATE_NUMBER'))
            )->response()->getData(true)
        );
    }
}
