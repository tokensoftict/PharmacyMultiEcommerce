<?php

namespace App\Services\User\Supermarket;

use App\Models\Address;
use App\Models\SupermarketUser;
use App\Models\User;
use Illuminate\Support\Arr;

class SupermarketCustomerService
{
    /**
     * @param array $data
     * @return SupermarketUser
     */
    public final function createCustomer(array $data, User $user) : SupermarketUser
    {
        $customer = SupermarketUser::where("user_id", $user->id)->first();

        if(!$customer) {
            $data = Arr::only(
                $data, ['address_id']
            );

            $data['user_id'] = $user->id;
            $customer  = SupermarketUser::create($data);
        }else{

            $customer = $this->updateCustomer($customer->user_id, $data);
        }

        return $customer;
    }


    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public final function updateCustomer(int $id, array $data) : SupermarketUser
    {
        $customer = SupermarketUser::findorfail($id);

        if($customer)
        {
            $customer->update($data);
        }

        return $customer->fresh();
    }


    /**
     * @param SupermarketUser $user
     * @return SupermarketUser
     */
    public final function activateBusiness(SupermarketUser $user) : SupermarketUser
    {
        $user->status = !$user->status;

        return $user->fresh();
    }


    /**
     * @param Address $address
     * @param SupermarketUser $wholesalesUser
     * @return SupermarketUser
     */
    public final function attachDefaultAddressToCustomer(Address $address, SupermarketUser $wholesalesUser) : SupermarketUser
    {
        $wholesalesUser->address_id = $address->id;
        $wholesalesUser->update();
        return $wholesalesUser->fresh();
    }


}
