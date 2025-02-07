<?php

namespace App\Repositories;

use App\Models\DeliveryMethod;

class DoiRepository
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
        if(is_string($extraData['template_settings_value'])) {
            $extraData['template_settings_value'] = json_decode($extraData['template_settings_value'], true);
        }

        $deliverySelected = collect($methodOfDelivery->template_settings_value)->filter(function ($value) use ($extraData){
            return $value['name'] == $extraData['template_settings']['name'];
        })->first();

        $error = [];

        $templateSettingsValue = $extraData['template_settings_value'];

        foreach ($deliverySelected['option'] as $option) {
            if(!isset($templateSettingsValue[$option['name']]) or empty($templateSettingsValue[$option['name']])) {
                $error[] = $option['name']. " is required to use this delivery method.";
            }
        }

        if(count($error) > 0){
            return [
                "status" => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => $error
            ];
        }


        return [
            "status" => true,
            'name'=>$methodOfDelivery->name.'[ '.$deliverySelected['name'].' ]',
            'amount'=>$deliverySelected['amount'],
        ];

    }

}
