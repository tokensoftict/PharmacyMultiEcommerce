<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\ApiController;
use App\Models\Productcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCategoryPushController extends ApiController
{
    public function __invoke(Request $request) : JsonResponse
    {
        return DB::transaction(function() use($request){

            $data = $request->get("data");

            if($request->has("action")){

                $model = match ($request->get('action')){
                    'new' => Productcategory::create($data),
                    'update' => Productcategory::where("id", $data)->update($data),
                    'destroy' => Productcategory::where("id", $data)->delete()
                };

                return $this->showOne($model);
            }else if(count($data) > 1){
                // this is a bulk insert so where to use Bulk insertion method
                DB::table("productcategories")->insert($data);

                return $this->sendSuccessResponse([]);
            }

            return $this->errorResponse("Unknown Action", Response::HTTP_BAD_REQUEST);
        });
    }
}
