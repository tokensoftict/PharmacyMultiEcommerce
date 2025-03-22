<?php

namespace App\Services\User\Wholesales;

use App\Models\Address;
use App\Models\User;
use App\Models\WholesalesUser;
use Illuminate\Support\Arr;

class WholeSalesCustomerService
{

    /**
     * @param array $data
     * @param User $user
     * @return WholesalesUser
     */
    public final function createCustomer(array $data, User $user) : WholesalesUser
    {
        $customer = WholesalesUser::where("user_id", $user->id)->first();

        if(!$customer) {
            $data = Arr::only(
                $data,
                ['customer_group_id', 'business_name', 'type', 'cac_document', 'premises_licence', 'phone', 'address_id']
            );


            $data['user_id'] = $user->id;
            $customer  = WholesalesUser::create($data);

            $cac = business_certificate()->addMediaFromRequest("cac_document")->toMediaCollection('medialibrary');
            $premises = premises_licence()->addMediaFromRequest("premises_licence")->toMediaCollection('medialibrary');

            $customer->cac_document = getMediaFullPath($cac);
            $customer->premises_licence = getMediaFullPath($premises);
            $customer->save();

        }else{

            $customer = $this->updateCustomer($customer->user_id, $data);
        }

        return $customer;
    }


    /**
     * @param int $id
     * @param array $data
     * @return WholesalesUser
     */
    public final function updateCustomer(int $id, array $data) : WholesalesUser
    {
        $customer = WholesalesUser::findorfail($id);

        if($customer)
        {
            $customer->update($data);
        }

        return $customer->fresh();
    }


    /**
     * @param WholesalesUser $user
     * @return WholesalesUser
     */
    public final function activateBusiness(WholesalesUser $user) : WholesalesUser
    {
        $user->status = !$user->status;

        return $user->fresh();
    }


    /**
     * @param Address $address
     * @param WholesalesUser $wholesalesUser
     * @return WholesalesUser
     */
    public final function attachDefaultAddressToCustomer(Address $address, WholesalesUser $wholesalesUser) : WholesalesUser
    {
        $wholesalesUser->address_id = $address->id;
        $wholesalesUser->update();
        return $wholesalesUser->fresh();
    }

}
