<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends ApiController
{
    /**
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $request->validate([
            'old_password' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        $oldPassword = $request->input('old_password');
        $newPassword = $request->input('password');

        $check = Hash::check($oldPassword, $user->password);

        if(!$check) return $this->errorResponse("Old password is not correct, please check and try again.", 400);

        $user->password = bcrypt($newPassword);
        $user->save();

       return $this->sendSuccessResponse(["message" => "Your Password has been changed successfully."]);
    }
}
