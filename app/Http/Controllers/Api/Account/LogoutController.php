<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $user = auth("sanctum")->user();
        $user->updateDeviceKey(NULL);
        $schedulesIDS = $user->medReminderSchedule()->pluck("id")->toArray();
        auth("sanctum")->user()->tokens()->delete();
        return $this->sendSuccessResponse($schedulesIDS);
    }
}
