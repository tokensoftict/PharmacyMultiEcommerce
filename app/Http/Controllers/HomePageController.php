<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomePageController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public final function pushIndex() : JsonResponse
    {
        return $this->sendSuccessResponse(['status' => Cache::get("app:server-push-status")]);
    }

}
