<?php

namespace App\Repositories;

class PatRepository
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

    public function confirm_payment($payment, $data){
        $settings = json_decode($payment->template_settings_value,true);
        return array("status" => true, "data" => $settings);
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
