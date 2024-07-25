<?php

namespace App\Repositories;

class BankRepository
{
    public function __constuct()
    {
        //
    }

    public function delete($request,$payment){
        $settings = $payment->template_settings_value;
        $settings = json_decode($settings,true);
        unset($settings[$request->index]);
        $newset = [];
        foreach($settings as $setting){
            $newset[] = $setting;
        }
        $payment->template_settings_value = json_encode($newset);
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

    public function confirm_payment($payment, $data){
        $settings = json_decode($payment->template_settings_value,true);
        return array("status" => true, "data" => $settings);
    }

    public function checkouttemplate($payment){
        $html = '<br/><h5>Bank List(s)</h5>';
        $settings = json_decode($payment->template_settings_value,true);
        $html.='<table class="table table-responsive table-bordered table-striped">';
            $html.='<thead><tr><th>#</th><th>Bank</th><th>Bank Account No</th><th>Account Name</th></tr></thead><tbody>';
            $num = 1;
             foreach($settings as $setting){
                 $html.='<tr>';
                 $html.='<td>'.$num.'</td>';
                 $html.='<td>'.$setting['name'].'</td>';
                 $html.='<td>'.$setting['number'].'</td>';
                 $html.='<td>'.$setting['bank'].'</td>';
                 $html.='</tr>';
                 $num ++;
             }
        $html.='</tbody></table>';

        return $html;
    }

    public function checkouttemplateMobile($payment){
        $html = '<strong>Bank List(s)</strong><br/>';
        $settings = json_decode($payment->template_settings_value,true);
        foreach($settings as $setting){
            $html.='<strong>Bank Name: </strong>'.$setting['name']."<br/>";
            $html.='<strong>Bank Number: </strong>'.$setting['number']."<br/>";
            $html.='<strong>Bank Account name: </strong>'.$setting['bank']."<br/>";
            $html.="<br/> <hr/>";
        }
        return $html;
    }
}
