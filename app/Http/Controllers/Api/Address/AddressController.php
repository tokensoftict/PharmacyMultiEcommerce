<?php

namespace App\Http\Controllers\Api\Address;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Address\AddressListResource;
use App\Models\Address;
use App\Services\User\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AddressController extends ApiController
{
    public AddressService $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }


    /**
     * @param Address $address
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Address $address, Request $request) : JsonResponse
    {
        return $this->showOne(new AddressListResource($address));
    }
}
