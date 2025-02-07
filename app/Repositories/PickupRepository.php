<?php

namespace App\Repositories;

use App\Models\DeliveryMethod;

class PickupRepository
{
    /**
     * @param array|null $shoppingCart
     * @param DeliveryMethod $methodOfDelivery
     * @param array|null $extraData
     * @return array
     */
    public final function calculateDeliveryTotal(?array $shoppingCart, DeliveryMethod $methodOfDelivery , ?array $extraData) : array
    {
        $applicationUser = getApplicationModel();

        if(!$applicationUser) return [
            'status' => false,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
            'error' => ["Application user error, Please restart the application to complete your checkout"]
        ];

        return [
            'status' => true,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
        ];
    }
}
