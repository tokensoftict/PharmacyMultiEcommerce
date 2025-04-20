<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Services\Kafka\ProcessGeneralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ManufacturerPushController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        return DB::transaction(function() use($request){

            $data = $request->get("data");

            if($request->has("action")){

                $model = match ($request->get('action')){
                    'new' => ProcessGeneralService::createManufacturer($data),
                    'update' => ProcessGeneralService::updateManufacturer($data),
                    'destroy' => Manufacturer::where("id", $data)->delete()
                };

                return $this->showOne($model);
            }else if(count($data) > 1){
                // this is a bulk insert so where to use Bulk insertion method
                ProcessGeneralService::createManufacturer($data);

                return $this->sendSuccessResponse([]);
            }

            return $this->errorResponse("Unknown Action", Response::HTTP_BAD_REQUEST);
        });
    }
}
