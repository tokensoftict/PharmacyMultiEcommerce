<?php

namespace App\Repositories;

use App\Models\PaymentGatewayTransactionLog;
use App\Models\PaymentMethod;
use App\Services\Api\Checkout\TransactionLogService;

defined('PAYSTACK_VERIFY_URL') OR define("PAYSTACK_VERIFY_URL","https://api.paystack.co/transaction/verify/");

class PaystackRepository
{

    /**
     * @param PaymentMethod $paymentMethod
     * @param array|null $data
     * @return array
     */
    public final function confirmPayment(PaymentMethod $paymentMethod, ?array $data) : array
    {
        $settings = $paymentMethod->template_settings_value;
        $confirm = $this->validatePayStackPayment($data['reference'], $settings['sec_key']);

        return $confirm;
    }


    /**
     * @param $ref
     * @param $sec_key
     * @return array
     */
    public final function validatePayStackPayment($ref, $sec_key) : array
    {
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
                        $log = PaymentGatewayTransactionLog::where('transaction_reference', $ref)->first();
                        if($log) {
                            app(TransactionLogService::class)->makeAsSuccessful($log);
                        }
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


    /**
     * @param $total
     * @return array
     */
    public final function calculateCharges(int|float $total) : array
    {
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
            'name'=>"PayStack Local Transaction Charge",
            'amount'=>$charges
        ];
    }

    /**
     * @param int|float $val
     * @return bool
     */
    function is_decimal(int|float $val) : bool
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}
