<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\GeneralResource;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Models\Manufacturer;
use App\Models\Productcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $productCategory = Productcategory::query()->with([
            'stocks' => fn ($query) => $query->limit(3)
        ])
            ->has('stocks', '>', 2);

        if($request->has('s')) {
            $productCategory->where('name', 'like', '%'.$request->get('s').'%');
        }

        $productCategory->select("id", "name")->where("status", 1);

        return $this->sendPaginatedSuccessResponse(
            GeneralResource::collection(
                $productCategory ->orderBy("name", "ASC")
                    ->paginate(config('app.PAGINATE_NUMBER'))
            )->response()->getData(true)
        );
    }
}
