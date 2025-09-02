<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\UpdateAccountRequest;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Services\User\UserAccountService;
use Illuminate\Http\JsonResponse;


class UpdateAccountController extends ApiController
{
    public UserAccountService $userAccountService;

    public function __construct(UserAccountService $userAccountService)
    {
        $this->userAccountService = $userAccountService;
    }

    /**
     * @param UpdateAccountRequest $request
     * @return JsonResponse
     */
    public function __invoke(UpdateAccountRequest $request) : JsonResponse
    {
        $user = $request->user();
        $user = $this->userAccountService->updateUserAccount($user->id, $request->validated());

        return $this->showOne(
            new UserLoginResource($user)
        );

    }

}
