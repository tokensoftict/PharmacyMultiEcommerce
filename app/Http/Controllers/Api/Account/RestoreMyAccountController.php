<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\DeleteMyAccountRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class RestoreMyAccountController  extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $userId = $request->get("user_id");
        $user = User::withTrashed()->find($userId);
        if($user) {
            $user->restore();
        }
        return $this->sendSuccessMessageResponse("Your account restored successfully.");
    }

}
