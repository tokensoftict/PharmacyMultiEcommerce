<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Auth\UserLoginResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $user = $request->user();
        $user->updateDeviceKey($request->get("deviceKey", false));
        return $this->showOne(
            new UserLoginResource($user)
        );
    }
}
