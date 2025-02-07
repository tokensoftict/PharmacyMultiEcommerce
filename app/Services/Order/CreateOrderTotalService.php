<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderTotalOrder;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;

class CreateOrderTotalService
{
    private SupermarketUser|WholesalesUser  $checkOutUser;
    public function __construct()
    {
        $this->checkOutUser = getApplicationModel();
    }


    /**
     * @param array $attributes
     * @return array
     */
    public final function formatOrderTotal(array $attributes) : array
    {
        return [
            'order_total_id' => $attributes['id'] ?? NULL,
            'name' => $attributes['name'],
            'value' => $attributes['amount']
        ];
    }


    /**
     * @return array
     * @throws \Exception
     */
    public final function prepareOrderTotal() : array
    {
        $orderTotalOrder = [];

        $subTotal = $this->checkOutUser->getUserCheckoutSubTotal();

        if($subTotal['status'] !== true) {
            throw new \Exception("Unable prepare order total for sub total");
        }
        foreach ($subTotal['items'] as $orderTotal) {
            $orderTotalOrder[] = new OrderTotalOrder(
                $this->formatOrderTotal($orderTotal),
            );
        }


        $orderTotals = $this->checkOutUser->getUserCheckOutOrderTotal();
        if($orderTotals['status'] !== true) {
            throw new \Exception("Unable prepare order total");
        }

        foreach ($orderTotals['items'] as $orderTotal) {
            if($orderTotal['autoCheck']) {
                $orderTotalOrder[] = new OrderTotalOrder(
                    $this->formatOrderTotal($orderTotal),
                );
            }
        }



        $deliveryOrderTotal = $this->checkOutUser->getUserCheckDeliveryTotal();
        if($deliveryOrderTotal['status'] !== true) {
            throw new \Exception("Unable prepare order total for delivery");
        }
        foreach ($deliveryOrderTotal['items'] as $orderTotal) {
            if($orderTotal['autoCheck']) {
                $orderTotalOrder[] = new OrderTotalOrder(
                    $this->formatOrderTotal($orderTotal),
                );
            }
        }

        return $orderTotalOrder;
    }

    /**
     * @param Order|int $order
     * @return Order
     * @throws \Exception
     */
    public final function create(Order|int $order) : Order
    {
        $order->order_total_orders()->saveMany($this->prepareOrderTotal());
        return $order->fresh();
    }

}
