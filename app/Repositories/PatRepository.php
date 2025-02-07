<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PatRepository
{
    /**
     * @param PaymentMethod $paymentMethod
     * @param array|null $data
     * @return array
     */
    public final function confirmPayment(PaymentMethod $paymentMethod, ?array $data) : array
    {
        $settings = $paymentMethod->template_settings_value;
        return [
            "status" => true,
            "data" => $settings
        ];
    }

}
