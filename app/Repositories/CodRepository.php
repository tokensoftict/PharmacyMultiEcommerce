<?php

namespace App\Repositories;

class CodRepository
{
    public function __constuct()
    {
        //
    }

    public function delete($request,$payment){
        $settings = $payment->template_settings_value;
        $settings = json_decode($settings,true);
        unset($settings[$request->index]);
        $payment->template_settings_value = json_encode($settings);
        $payment->update();
        return $payment;
    }

    public function add($request,$payment){
        $settings = $payment->template_settings_value;
        $settings = json_decode($settings,true);
        $settings[] = $request->data;
        $payment->template_settings_value = json_encode($settings);
        $payment->update();
        return $payment;
    }

    public function checkouttemplateMobile($payment){
        return "";
    }
}
