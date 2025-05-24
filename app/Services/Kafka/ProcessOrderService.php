<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Services\Order\CreateOrderService;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessOrderService
{

    public static function handle(ConsumedMessage $message) : void
    {
        $body = $message->getBody();;
        $action =  $body['action'];
        $data = $body[0];

        if($action === KafkaAction::PROCESS_ORDER) return;

        $createOrderService = new CreateOrderService();

        switch ($data['status_code']) {
            case "Validation Error":
                $createOrderService->processOrderError($data['orderId'], $data['error']);
                break;
            case "Packing" :
                $createOrderService->packOrder($data['orderId']);
                break;
            case "Cancelled" :
                $createOrderService->cancelOrder($data['orderId']);
                break;
            case "Waiting For Payment":
                $createOrderService->waitingForPaymentOrder($data['orderId']);
                break;
            case "Payment Confirmed":
                $createOrderService->confirmOrderPayment($data['orderId']);
                break;
            case "Dispatched":
                $createOrderService->dispatchedOrder($data['orderId'], $data['carton']);
            default:
        }

    }
}
