<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class DeleteMyAccountController  extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $userId = $request->get("user_id");
        $user = User::find($userId);
        if($user) {
            $user->tokens()->delete();
            $user->delete();
        }
        return $this->sendSuccessMessageResponse("Your account has been deleted.");
    }

}
