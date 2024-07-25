<?php
namespace App\Traits;

use App\Models\Address;

trait ApplicationUserTrait
{

    /**
     * @param Address|int $address
     * @return void
     */
    public final function setDefaultAddress(Address|int $address) : void
    {
        if(! $address instanceof Address){
            $address = Address::find($address);
        }

        $this->address_id = $address->id;
        $this->update();
    }


    /**
     * @param Address $address
     * @return bool
     */
    public final function checkIfDefaultAddressIs(Address $address) : bool
    {
        return $this->address_id == $address->id;
    }


    /**
     * @return void
     */
    public final function removeDefaultAddress() : void
    {
        $this->address_id = NULL;
        $this->update();
    }
}
