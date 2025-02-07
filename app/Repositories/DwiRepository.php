<?php

namespace App\Repositories;

use App\Models\DeliveryMethod;
use PhpParser\Node\NullableType;

class DwiRepository
{

    /**
     * @param array|null $shoppingCart
     * @param DeliveryMethod $methodOfDelivery
     * @param array|null $extraData
     * @return array
     */
    public function calculateDeliveryTotal(?array $shoppingCart, DeliveryMethod $methodOfDelivery , ?array $extraData) : array
    {

        if(is_string($extraData['template_settings'])) {
            $extraData['template_settings'] = json_decode($extraData['template_settings'], true);
        }

        $deliverySelected = collect($methodOfDelivery->template_settings_value)->filter(function ($value) use ($extraData){
            return $value['name'] == ( $extraData['template_settings']['name'] ??  $extraData['template_settings']['title']);
        })->first();

        if(!$deliverySelected){
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Unable able to determine your delivery price, please try again!']
            ];
        }

        return [
            'status' => true,
            'name'=>$methodOfDelivery->name.'[ '.$deliverySelected['name'].' ]',
            'amount'=>$deliverySelected['amount'],
        ];
    }

}
