<?php

namespace App\Repositories;

class DwiRepository
{
    public function __constuct()
    {
        //
    }

    public function delete($request,$shipping){
        $settings = $shipping->template_settings_value;
        $settings = json_decode($settings,true);
        unset($settings[$request->index]);
        $newset = [];
        foreach($settings as $setting){
            $newset[] = $setting;
        }
        $shipping->template_settings_value = json_encode($newset);
        $shipping->update();
        return $shipping;
    }

    public function add($request,$shipping){
        $settings = $shipping->template_settings_value;
        $settings = json_decode($settings,true);
        $settings[] = $request->data;
        $shipping->template_settings_value = json_encode($settings);
        $shipping->update();
        return $shipping;
    }

    public function calculate_delivery_code($delivery, $deliverydata){
        $location = $delivery['location'];
        $template_settings_value = $deliverydata->template_settings_value;
        $template_settings_value = json_decode($template_settings_value , true);
        $selected = $template_settings_value[$location];
        return [
          'name'=>$deliverydata->name.'[ '.$selected['name'].' ]',
          'amount'=>$selected['amount'],
        ];
    }

    public function get_dynamic_data($checkoutData)
    {
        return [];
    }

}
