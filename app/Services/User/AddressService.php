<?php

namespace App\Services\User;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class AddressService
{

    /**
     * @param array $data
     * @return Address
     */
    public final function createAddress(User $user,array $data) : Address
    {
        $data = Arr::only($data, ['name', 'address_1', 'address_2', 'country_id', 'state_id', 'town_id']);

        $data['user_id'] = $user->id;

        return Address::create($data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Address
     */
    public final function updateAddress(Address|int $address, array $data) : Address
    {
        $data = Arr::only($data,  ['name', 'address_1', 'address_2', 'country_id', 'state_id', 'town_id']);

        if(! $address instanceof Address){
            $address = Address::findorfail($address);
        }

        if($address) $address->update($data);

        return $address->fresh();
    }

    /**
     * @param Address $address
     * @return bool
     */
    public final function deleteAddress(Address $address) : bool
    {
        $address->deleted = 1;
        return $address->update();
    }

    /**
     * @return Collection
     */
    public final function getAddresses(User|null|int $user) : Collection
    {
        $addresses =  Address::where("deleted", "0");

        if(! $user instanceof User){
            if(!is_null($user)) {
                $user = User::find($user);
            }
        }

        if(!is_null($user)){
            $addresses->where("user_id", $user->id);
        }

        return $addresses->get();
    }

    /**
     * @param Address $address
     * @return bool
     */
    public final function isDeletedThis(Address $address) : bool
    {
        return $address->deleted == "1";
    }
}
