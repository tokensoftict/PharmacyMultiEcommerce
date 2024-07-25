<?php

namespace App\Repositories;

class DoiRepository
{
    public function __constuct()
    {
        //
    }

    public function delete($request,$shipping){
        $settings = $shipping->template_settings_value;
        $settings = json_decode($settings,true);
        //unset($settings[$request->index]);
        $settings[$request->index]['delete_status'] = true;
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
        $wrapup = [];
        $pend = $request->data;
        if(isset($pend['option'])) {
            foreach ($pend['option']['name'] as $key => $r_wrap) {
                $wrapup[] = array(
                    'name' => $r_wrap,
                    'type' => $pend['option']['type'][$key],
                );
            }
        }
        $pend['option'] = $wrapup;
        $settings[] = $pend;
        $shipping->template_settings_value = json_encode($settings);
        $shipping->update();
        return $shipping;
    }


    public function calculate_delivery_code($delivery, $deliverydata){
        $location = $delivery['mod_index'];
        $template_settings_value = $deliverydata->template_settings_value;
        $template_settings_value = json_decode($template_settings_value , true);
        $selected = $template_settings_value[$location];
        return [
            'name'=>$deliverydata->name,
            'amount'=>$selected['amount']
        ];
    }

    public function get_dynamic_data($checkoutData)
    {
        return [];
    }
}
