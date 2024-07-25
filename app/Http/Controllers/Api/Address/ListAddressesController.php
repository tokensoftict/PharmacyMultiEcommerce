<?php

namespace App\Http\Controllers\Api\Address;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Address\AddressListResource;
use App\Services\User\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ListAddressesController extends ApiController
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
    public function __invoke(Request $request) : JsonResponse
    {
        $user = $request->user();
        return $this->showAll(AddressListResource::collection($this->addressService->getAddresses($user)));
    }
}
