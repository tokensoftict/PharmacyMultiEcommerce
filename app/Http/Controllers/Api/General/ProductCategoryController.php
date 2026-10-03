<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\ApplicationEnvironment;
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
        $productCategory = Productcategory::query()
            ->where('status', 1)
            ->whereHas('stocks', function ($query) {
                $query
                    ->whereHas(ApplicationEnvironment::$stock_model_string, function ($query) {
                        $query->where('quantity', '>', 1);
                    })
                    ->where('admin_status', 1);
            }, '>', 2)
            ->with([
                'stocks' => fn ($query) => $query
                    ->whereHas(ApplicationEnvironment::$stock_model_string, function ($query) {
                        $query->where('quantity', '>', 1);
                    })
                    ->where('admin_status', 1)
                    ->limit(3),
            ])
            ->select('id', 'name');

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
