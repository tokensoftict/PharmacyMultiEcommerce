<?php

namespace App\Services\Order;

use App\Classes\ApplicationEnvironment;
use App\Events\Order\OrderStatusUpdatedEvent;
use App\Models\Order;
use App\Models\Status;
use App\Services\Api\Checkout\ConfirmOrderService;

class CreateOrderService
{
    /**
     * @param array $attributes
     * @return array
     * @throws \Exception
     */
    private function formatOrderAttributes(array &$attributes) : array
    {
        $checkoutUser = getApplicationModel();
        if(!$checkoutUser) {
            throw new \Exception("Application user error, Please restart the application to complete your checkout");
        }

        $attributes['order_id'] = $attributes['order_id'] ?? generateRandomString(10);
        $attributes['invoice_no'] = $attributes['invoice_no'] ?? generateUniqueNumber();
        $attributes['order_date'] = $attributes['order_date'] ?? todaysDate();
        $attributes['customer_type'] = $attributes['customer_type'] ?? get_class($checkoutUser);
        $attributes['customer_id'] = $attributes['customer_id'] ?? $checkoutUser->id;

        $attributes['ip'] =  $attributes['ip'] ?? request()->ip();
        $attributes['user_agent'] = $attributes['user_agent'] ?? request()->userAgent();

        $attributes['customer_group_id'] = $attributes['customer_group_id'] ?? $checkoutUser->customer_group_id;
        $attributes['firstname'] = $attributes['firstname'] ?? $checkoutUser->user->firstname;
        $attributes['lastname'] = $attributes['lastname'] ?? $checkoutUser->user->lastname;
        $attributes['email'] = $attributes['email'] ?? $checkoutUser->user->email;
        $attributes['telephone'] = $attributes['telephone'] ??  $checkoutUser?->telephone ?? $checkoutUser->user->phone;
        $attributes['payment_method_id'] = $attributes['payment_method_id'] ??  $checkoutUser->getCheckoutPaymentMethod();
        $attributes['delivery_method_id'] = $attributes['delivery_method_id'] ?? $checkoutUser->getCheckoutDeliveryMethod()['deliveryMethod'];
        $attributes['comment'] = $attributes['comment'] ?? " ";
        $attributes['status_id'] = $attributes['status_id'] ?? status("Pending");
        $attributes['payment_address_id'] = $checkoutUser->getCheckoutAddress() ?? $checkoutUser->getDefaultAddress();
        $attributes['shipping_address_id'] = $checkoutUser->getCheckoutAddress() ?? $checkoutUser->getDefaultAddress();
        $attributes['checkout_data'] = $attributes['checkout_data'] ?? $checkoutUser->checkout;
        $attributes['ordertotals'] =  $attributes['ordertotals'] ?? ($checkoutUser->ordertotals ?? NULL);
        $attributes['app_id'] = ApplicationEnvironment::$id;
        $attributes['sales_representative_id'] = $attributes['sales_representative_id'] ?? ($checkoutUser->sales_representative_id ?? NULL);
        $attributes['coupon_information'] = $checkoutUser->isCouponCode() ? $checkoutUser->coupon_data : NULL;
        $attributes['voucher_information'] = $checkoutUser->isVoucherCode() ? $checkoutUser->coupon_data : NULL;
        $attributes['cart_cache'] = $checkoutUser->cart;
        if(!isset($attributes['total'])) {
            $checkOutOrderService = new ConfirmOrderService();
            $total = $checkOutOrderService->confirmOrderReturnTotal();
            if(isset($total['status']) && $total['status'] === FALSE) {
                throw new \Exception($total['message']);
            }
            $attributes['total'] = $total;
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @return Order
     * @throws \Exception
     */
    public final function create(array $attributes) : Order
    {
        $attributes = $this->formatOrderAttributes($attributes);
        return Order::create($attributes);
    }

    /**
     * @param Order|int $order
     * @param array $attributes
     * @return Order
     */
    public final function updateOrder(Order|int $order, array $attributes) : Order
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }
        $order->update($attributes);
        return $order->fresh();
    }


    /**
     * @param Order|int $order
     * @param Status|int $status
     * @return Order
     */
    public final function updateOrderStatus(Order|int $order, Status|int $status) : Order
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if(!$status instanceof Status) {
            $status = Status::findorfail($status);
        }

        $order->update(['status_id' => $status->id]);
        $order = $order->fresh();

        event(new OrderStatusUpdatedEvent($order));

        return $order;
    }
}
