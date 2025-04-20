<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockPrice;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use App\Services\Kafka\ProcessGeneralService;
use App\Services\Kafka\ProcessStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StockPushController extends ApiController
{
    public function __invoke(Request $request) : JsonResponse
    {
        return DB::transaction(function() use($request){

            $data = $request->get("data");

            if($request->has("action")){

                $model = match ($request->get('action')){
                    'new' => ProcessStockService::createStock($data),
                    'update' => ProcessStockService::updateStock($data),
                    'destroy' => Stock::where("id", $data)->delete()
                };

                return $this->showOne($model);
            }else if(count($data) > 1){
                // this is a bulk insert so where to use Bulk insertion method
                foreach ($data as $stock){
                    $wholesales = $stock['stock_prices']['wholesales'] ?? false;
                    $supermarket = $stock['stock_prices']['supermarket'] ?? false;
                    unset($data['stock_prices']);
                    $pushStock = Stock::updateOrCreate([
                        "local_stock_id" => $stock['local_stock_id']
                    ], $stock);

                    if($wholesales) {
                        $wholesales = new WholessalesStockPrice($wholesales);
                        $pushStock->wholessales_stock_prices()->save($wholesales);
                    }
                    if($supermarket) {
                        $supermarket = new SupermarketsStockPrice($supermarket);
                        $pushStock->supermarkets_stock_prices()->save($supermarket);
                    }

                }

                return $this->sendSuccessResponse([]);
            }

            return $this->errorResponse("Unknown Action", Response::HTTP_BAD_REQUEST);
        });
    }
}
