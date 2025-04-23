<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\SignUpRequest;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Services\User\AppUserService;
use App\Services\User\Supermarket\SupermarketCustomerService;
use App\Services\User\UserAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SignupController extends ApiController
{

    public UserAccountService $userAccountService;
    public SupermarketCustomerService $supermarketCustomerService;

    public AppUserService $appUserService;

    public function __construct(UserAccountService $userAccountService, SupermarketCustomerService $supermarketCustomerService, AppUserService $appUserService)
    {
        $this->userAccountService = $userAccountService;
        $this->supermarketCustomerService = $supermarketCustomerService;
        $this->appUserService = $appUserService;
    }

    /**
     * @param SignUpRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function __invoke(SignUpRequest $request) : JsonResponse
    {
        $user = User::withTrashed()->where(function ($query) use($request) {
            $query->orWhere("email", $request->email)->orWhere("phone", $request->phone);
        })->first();

        if($user and $user->trashed()){
            return $this->sendSuccessResponse([
                'trashed' => $user->trashed(),
                'user' => new UserLoginResource($user),
            ]);
        }

        return DB::transaction(function() use ($request){
            $user = $this->userAccountService->createUserAccount($request->only([
                "email",
                "firstname",
                "lastname",
                "phone",
                "password",
            ]));

            // let's create a supermarket account for the user since supermarket does not need
            // any administrator validation
            $superMarketUser = $this->supermarketCustomerService->createCustomer(['phone' => $user->phone], $user);

            $this->appUserService->createAppUser($user, $superMarketUser);
            //link the supermarket account app users so the user can be assign to supermarket customer

            $user->updateDeviceKey($request->get("deviceKey", false));
            //update the user device key for push notification

            $user = $user->refresh();

            return $this->showOne(new UserLoginResource($user));
        });
    }
}
