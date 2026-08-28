<?php

namespace App\Http\Controllers\Utilities\Json\Stock;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utilities\Json\Stock\Select2Resource;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class StockUtilitiesController extends Controller
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public final function searchForStock(Request $request) : AnonymousResourceCollection|JsonResponse
    {
        $searchTerm  = $request->get('searchTerm') ?? $request->get('s');
        if($searchTerm == "") return response()->json([], 200);
        dump(ApplicationEnvironment::$stock_model_string. "hellomworld");
        return Select2Resource::collection(
            Stock::query()->select('id', 'name')
                ->where('name', 'like', "%$searchTerm%")->get()
        );
    }
}
