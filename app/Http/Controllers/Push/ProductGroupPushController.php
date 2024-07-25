<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Models\Productgroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductGroupPushController extends ApiController
{
    public function __invoke(Request $request) : JsonResponse
    {
        return DB::transaction(function() use($request){

            $data = $request->get("data");

            if($request->has("action")){

                $model = match ($request->get('action')){
                    'new' => Productgroup::create($data),
                    'update' => Productgroup::where("id", $data)->update($data),
                    'destroy' => Productgroup::where("id", $data)->delete()
                };

                return $this->showOne($model);

            }else if(count($data) > 1){
                // this is a bulk insert so where to use Bulk insertion method
                DB::table("productgroups")->insert($data);

                return $this->sendSuccessResponse([]);
            }

            return $this->errorResponse("Unknown Action", Response::HTTP_BAD_REQUEST);
        });
    }
}
