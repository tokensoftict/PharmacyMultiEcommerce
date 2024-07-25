<?php

namespace App\Repositories;

defined('PAYSTACK_VERIFY_URL') OR define("PAYSTACK_VERIFY_URL","https://api.paystack.co/transaction/verify/");

class PaystackRepository
{
    public function __constuct()
    {

    }
    public function add($request,$payment){
        $payment->template_settings_value = json_encode($request->data);
        $payment->update();
        return $payment;
    }

    public function confirm_payment($payment, $data){
        $settings = json_decode($payment->template_settings_value,true);
        return $this->validate_paystack_payment($data['reference'], $settings['sec_key']);
    }


    public function validate_paystack_payment($ref, $sec_key){
        $result = array();
        $url = PAYSTACK_VERIFY_URL . $ref;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' .$sec_key]
        );
        $request = curl_exec($ch);
        curl_close($ch);
        if ($request) {
            $result = json_decode($request, true);
            if ($result) {
                if ($result['data']) {
                    if ($result['data']['status'] == 'success') {
                        return array("status" => true, "data" => $result);
                    } else {
                        return array("status" => false, "message" => $result);
                    }
                } else {
                    return array("status" => false, "message" => $result['message']);
                }
            } else {
                return array("status" => false, "message" => "Something went wrong while trying to convert the request variable to json.");

            }
        } else {
            return array("status" => false, "message" => "Something went wrong while retrieving payment confirmation");

        }
    }

    public function checkouttemplate($payment){
        return '';
    }

    public function checkouttemplateMobile($payment){
        return "";
    }

    public function calculate_charges($total){
        $percent = 1.5;
        $charges = ($percent/100) * $total;
        if($total > 2500){
            $charges+=100;
        }
        if($this->is_decimal($charges)){
            $charges=$charges+1;
            $charges = round($charges);
        }else{
            $charges = round($charges,2);
        }
        return [
            'name'=>"Paystack Local Transaction Charge",
            'amount'=>$charges
        ];
    }

    function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
