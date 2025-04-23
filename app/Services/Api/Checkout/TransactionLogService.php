<?php

namespace App\Services\Api\Checkout;

use App\Models\PaymentGatewayTransactionLog;

class TransactionLogService
{

    public static $status = [
        "Pending" => 0,
        "Success" => 1,
        "Failed" => 2,
        "Cancelled" => 3,
    ];

    private PaymentGatewayTransactionLog $paymentGatewayTransactionLog;

    public function __construct(PaymentGatewayTransactionLog $paymentGatewayTransactionLog)
    {
        $this->paymentGatewayTransactionLog = $paymentGatewayTransactionLog;
    }

    /**
     * @param array $data
     * @return PaymentGatewayTransactionLog
     */
    public final function create(array $data) : PaymentGatewayTransactionLog
    {
        $data = \Arr::only($data, [
            "gateway",
            "total",
            "email",
            "phone",
            "currency",
            "user_id"
        ]);

        $data['transaction_reference'] = getRandomString_AlphaNum(10);
        $data['status'] = self::$status["Pending"];
        return $this->paymentGatewayTransactionLog->create($data);
    }


    /**
     * @param PaymentGatewayTransactionLog|int $gatewayTransactionLog
     * @return PaymentGatewayTransactionLog
     */
    public final function makeAsSuccessful(PaymentGatewayTransactionLog | int $gatewayTransactionLog) : PaymentGatewayTransactionLog
    {
        $gatewayTransactionLog = !$gatewayTransactionLog instanceof PaymentGatewayTransactionLog ?
            $this->paymentGatewayTransactionLog->find($gatewayTransactionLog) : $gatewayTransactionLog;
        $gatewayTransactionLog->status = self::$status["Success"];
        $gatewayTransactionLog->save();
        return $gatewayTransactionLog;
    }


    /**
     * @param PaymentGatewayTransactionLog|int $gatewayTransactionLog
     * @return PaymentGatewayTransactionLog
     */
    public final function makeAsFailed(PaymentGatewayTransactionLog | int $gatewayTransactionLog) : PaymentGatewayTransactionLog
    {
        $gatewayTransactionLog = !$gatewayTransactionLog instanceof PaymentGatewayTransactionLog ?
            $this->paymentGatewayTransactionLog->find($gatewayTransactionLog) : $gatewayTransactionLog;
        $gatewayTransactionLog->status = self::$status["Failed"];
        $gatewayTransactionLog->save();
        return $gatewayTransactionLog;
    }

    /**
     * @param PaymentGatewayTransactionLog|int $gatewayTransactionLog
     * @return PaymentGatewayTransactionLog
     */
    public final function makeAsCancel(PaymentGatewayTransactionLog | int $gatewayTransactionLog) : PaymentGatewayTransactionLog
    {
        $gatewayTransactionLog = !$gatewayTransactionLog instanceof PaymentGatewayTransactionLog ?
            $this->paymentGatewayTransactionLog->find($gatewayTransactionLog) : $gatewayTransactionLog;
        $gatewayTransactionLog->status = self::$status["Cancelled"];
        $gatewayTransactionLog->save();
        return $gatewayTransactionLog;
    }
}
