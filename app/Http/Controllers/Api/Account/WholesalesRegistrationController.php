<?php

namespace App\Http\Controllers\Api\Account;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\CreateWholeSalesUserRequest;
use App\Http\Requests\Api\Auth\SignUpRequest;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Models\SupermarketUser;
use App\Services\User\AddressService;
use App\Services\User\AppUserService;
use App\Services\User\Supermarket\SupermarketCustomerService;
use App\Services\User\UserAccountService;
use App\Services\User\Wholesales\WholeSalesCustomerService;
use App\Services\Referral\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WholesalesRegistrationController extends ApiController
{

    public UserAccountService $userAccountService;
    public WholeSalesCustomerService $wholesalesCustomerService;

    public AppUserService $appUserService;

    public AddressService $addressService;
    public ReferralService $referralService;

    public function __construct(
        UserAccountService $userAccountService,
        WholeSalesCustomerService $wholesalesCustomerService,
        AppUserService $appUserService,
        AddressService $addressService,
        ReferralService $referralService
    ) {
        $this->userAccountService = $userAccountService;
        $this->wholesalesCustomerService = $wholesalesCustomerService;
        $this->appUserService = $appUserService;
        $this->addressService = $addressService;
        $this->referralService = $referralService;
    }


    /**
     * @param CreateWholeSalesUserRequest $request
     * @return JsonResponse
     */
    public function __invoke(CreateWholeSalesUserRequest $request) : JsonResponse
    {
        return DB::transaction(function() use ($request){

            $user = $request->user();

            $wholesale = $this->wholesalesCustomerService->createCustomer($request->all() ,$user);
            $this->appUserService->createAppUser($user, $wholesale);

            $address = $this->addressService->createAddress($user, $request->all());
            $this->wholesalesCustomerService->attachDefaultAddressToCustomer($address, $wholesale);

            // ── Referral System: establish relationship if referral code provided ──
            $referralCode = $request->get('referral_code');
            if (!empty($referralCode)) {
                $this->referralService->createReferralRelationship(
                    $user,
                    strtoupper(trim($referralCode)),
                    'wholesales'
                );
            }

            return $this->sendSuccessMessageResponse('Business account has been created successfully');
        });
    }
}
