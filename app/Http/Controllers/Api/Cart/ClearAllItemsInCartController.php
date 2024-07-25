<?php

namespace App\Http\Controllers\Api\Cart;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ClearAllItemsInCartController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();

        $application->cart = [];
        $application->update();

        return $this->sendSuccessMessageResponse("All Item(s) in cart has been removed successfully");
    }
}
