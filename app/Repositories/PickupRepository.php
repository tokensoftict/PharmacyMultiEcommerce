<?php

namespace App\Repositories;

class PickupRepository
{
    public function __constuct()
    {
        //
    }

    public function add($request,$shipping){
        $shipping->template_settings_value = json_encode($request->data);
        $shipping->update();
        return $shipping;
    }

    public function calculate_delivery_code($delivery, $deliverydata){
        return [
            'name'=>$deliverydata->name,
            'amount'=>0
        ];
    }

    public function checkouttemplate($payment){
        return '';
    }

    public function checkouttemplateMobile($payment){
        return "";
    }

    public function get_dynamic_data($checkoutData)
    {
        return [];
    }
}
