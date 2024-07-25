<?php

namespace App\Http\Controllers\Api\Address;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Models\Address;
use App\Services\User\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RemoveAddressController extends ApiController
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

        if($application->checkIfDefaultAddressIs($address)){
            $application->removeDefaultAddress();
        }

        if($this->addressService->isDeletedThis($address)){
            return $this->errorResponse("Address has already been deleted", Response::HTTP_BAD_REQUEST);
        }

        $this->addressService->deleteAddress($address);

        return $this->sendSuccessMessageResponse("Address has been deleted successfully");
    }
}
