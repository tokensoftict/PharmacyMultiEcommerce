<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends ApiController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(PasswordResetRequest $request) : JsonResponse
    {
        $status = Password::reset(
            $request->only('pin', 'password', 'password_confirmation', 'phone'),
            function ($user) use ($request){
                $user->forceFill([
                    'password' =>bcrypt($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'pin' => Lang::get($status),
            ]);

        }

       return $this->sendSuccessResponse(["message" => Lang::get($status)]);
    }
}
