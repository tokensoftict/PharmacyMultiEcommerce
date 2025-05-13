<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Models\App;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class FrontEndAppsController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) :JsonResponse
    {
        $apps = App::whereIn('id', [6, 5])->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($apps as $app) {
            $data['apps'][] = [
                "app_id" => $app->id,
                "domain" => $app->domain,
                "description" => $app->description,
                "info" => [],
                "logo" => $app->logo,
                "name" => strtolower($app->name),
                "link" => $app->link,
                "addresses" => [],
                "last_seen" => false,
                "unregistered" => true,
                "status" => false
            ];
        }

        return $this->sendSuccessResponse($data);
    }
}
