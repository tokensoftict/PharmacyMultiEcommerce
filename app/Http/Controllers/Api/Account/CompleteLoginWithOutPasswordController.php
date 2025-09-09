<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\CompleteLoginWithOutPasswordRequest;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class CompleteLoginWithOutPasswordController extends ApiController
{

    /**
     * @param CompleteLoginWithOutPasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(CompleteLoginWithOutPasswordRequest $request) : JsonResponse
    {
        $column = is_numeric($request->email) ? "phone" : "email";

        $credentials = [$column => $request->email, 'auth_code' => $request->otp];

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'The code you entered is incorrect. Please double-check and try again',
            ]);
        }

        $user = User::withTrashed()->where($column, $request->email)->first();

        $user->auth_code = NULL;
        $user->save();

        if($user->trashed()){
            return $this->sendSuccessResponse([
                'trashed' => $user->trashed(),
                'user' => new UserLoginResource($user),
            ]);
        }


        auth()->attempt($credentials);

        $user = $request->user();

        $user->updateLastSeen();

        $user->tokens()->delete();

        $user->updateDeviceKey($request->get("deviceKey", false));

        return $this->showOne(new UserLoginResource($user));
    }

}
