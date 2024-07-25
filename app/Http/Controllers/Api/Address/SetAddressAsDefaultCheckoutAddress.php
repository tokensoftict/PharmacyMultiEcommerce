<?php

namespace App\Http\Controllers\Api\Address;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Address\AddressListResource;
use App\Models\Address;
use App\Services\User\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SetAddressAsDefaultCheckoutAddress extends ApiController
{

    public AddressService $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Address $address, Request $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();

        if($this->addressService->isDeletedThis($address)){
            return $this->errorResponse("Address not Found or has been Deleted", Response::HTTP_BAD_REQUEST);
        }

        $application->setDefaultAddress($address);

        return $this->showOne(new AddressListResource($address));
    }

}
