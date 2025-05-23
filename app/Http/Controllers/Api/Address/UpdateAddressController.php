<?php

namespace App\Http\Controllers\Api\Address;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Address\CreateNewAddressRequest;
use App\Http\Resources\Api\Address\AddressListResource;
use App\Models\Address;
use App\Services\User\AddressService;
use Illuminate\Http\JsonResponse;


class UpdateAddressController extends ApiController
{
    public AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * @param CreateNewAddressRequest $request
     * @return JsonResponse
     */
    public function __invoke(Address $address, CreateNewAddressRequest $request) : JsonResponse
    {
        $user = $request->user();
        $applicationModel = ApplicationEnvironment::$appRelated;
        $application = $user->$applicationModel()->first();

        $address = $this->addressService->updateAddress(
            $address,
            [
                "name" => $request->get("name"),
                "address_1" => $request->get("address_1"),
                "address_2" => $request->get("address_1"),
                "country_id" => $request->get("country_id"),
                "state_id" => $request->get("state_id"),
                "town_id" => $request->get("town_id"),
            ]);

        if($request->has("setAsDefault") and $request->get("setAsDefault") === "1"){
            //set the default address to the newly create address
            $application->setDefaultAddress($address);
        }
        return $this->showOne(new AddressListResource($address));
    }
}
