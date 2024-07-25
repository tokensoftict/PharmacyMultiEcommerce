<?php

namespace App\Repositories;

class FlutterwaveRepository
{
    public function __constuct()
    {
        //
    }


    public function add($request,$payment){
        $payment->template_settings_value = json_encode($request->data);
        $payment->update();
        return $payment;
    }

    public function checkouttemplate($payment){
        return '';
    }

    public function checkouttemplateMobile($payment){
        return "";
    }
}
