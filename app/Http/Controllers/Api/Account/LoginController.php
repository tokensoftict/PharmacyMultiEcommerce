<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request) : JsonResponse
    {
        $column = is_numeric($request->email) ? "phone" : "email";

        $credentials = [$column => $request->email, 'password' => $request->password];

        $user = User::withTrashed()->where($column, $request->email)->first();

        if($user and $user->trashed()){
            return $this->sendSuccessResponse([
                'trashed' => $user->trashed(),
                'user' => new UserLoginResource($user),
            ]);
        }

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        auth()->attempt($credentials);

        $user = $request->user();

        $user->updateLastSeen();

        //$user->tokens()->delete();

        $user->updateDeviceKey($request->get("deviceKey", false));

        return $this->showOne(new UserLoginResource($user));
    }
}
