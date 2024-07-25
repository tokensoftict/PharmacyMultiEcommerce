<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Models\Classification;
use App\Models\NewStockArrival;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NewArrivalPushController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return DB::transaction(function() use($request){
            $localStockIDs = array_keys($request->get("data"));
            $localStock = $request->get("data");
            $stocks = Stock::whereIn("local_stock_id", $localStockIDs)->get();
            $newArrivalStocks = $stocks->map(function($stock) use ($request, $localStock){
                return [
                    "stock_id" => $stock->id,
                    "app_id" => $request->get("store"),
                    "quantity" =>$localStock[$stock->local_stock_id]['qty'],
                    "arrival_date" => date("Y-m-d")
                ];
            })->toArray();

            foreach ($newArrivalStocks as $newArrivalStock) {
                NewStockArrival::create($newArrivalStock);
            }

            return $this->sendSuccessMessageResponse("New arrival as been saved successfully");

        });
    }
}
