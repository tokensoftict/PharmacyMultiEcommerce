<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderTotal;
use App\Models\OrderTotalOrder;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;

class CreateOrderTotalService
{
    private SupermarketUser|WholesalesUser|bool  $checkOutUser;
    private float|int $total;
    public function __construct()
    {
        $this->checkOutUser = getApplicationModel();
        $this->total =0;
    }


    /**
     * @param array $attributes
     * @return array
     */
    public final function formatOrderTotal(array $attributes) : array
    {
        $this->total += ($attributes['amount'] ?? $attributes['value']);
        return [
            'order_total_id' => $attributes['order_total_id'] ?? ($attributes['id'] ?? NULL),
            'name' => $attributes['name'],
            'value' => $attributes['amount'] ?? $attributes['value']
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


        $paymentGateWayCharges = $this->checkOutUser->calculatePayStackCharges($this->total);
        if($paymentGateWayCharges['status'] === true) {
            $items = $paymentGateWayCharges['items'] ?? [];
            foreach ($items as $orderTotal) {
                if($orderTotal['autoCheck']) {
                    $orderTotalOrder[] = new OrderTotalOrder(
                        $this->formatOrderTotal($orderTotal),
                    );
                }
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

    /**
     * @param Order|int $order
     * @param array $orderTotals
     * @return Order
     */
    public final function createOrderTotalFromOlderServer(Order|int $order, array $orderTotals) : Order
    {
        $totals = [];
        foreach ($orderTotals as $orderTotal) {
            unset($orderTotal['id']);
            if(is_null($orderTotal['order_total_id'])) {
                $totals[] = new OrderTotalOrder(
                    $this->formatOrderTotal($orderTotal)
                );
            }else {
                $orderTotal['order_total_id'] = 1;
                $totals[] = new OrderTotalOrder(
                    $this->formatOrderTotal($orderTotal)
                );
            }
        }

        $order->order_total_orders()->saveMany($totals);
        return $order->fresh();
    }

}
