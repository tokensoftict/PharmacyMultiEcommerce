<?php

namespace App\Services\Order;

use App\Classes\ApplicationEnvironment;
use App\Enums\KafkaAction;
use App\Enums\KafkaEvent;
use App\Enums\PushNotificationAction;
use App\Events\Order\OrderStatusUpdatedEvent;
use App\Mail\Order\CancelledOrder;
use App\Mail\Order\ConfirmPayment;
use App\Mail\Order\DispatchedOrder;
use App\Mail\Order\NewOrderEmail;
use App\Mail\Order\OrderItemChanged;
use App\Mail\Order\PackedOrder;
use App\Mail\Order\ProcessingError;
use App\Mail\Order\WaitingForPayment;
use App\Models\Order;
use App\Models\Status;
use App\Services\Api\Checkout\ConfirmOrderService;
use App\Services\Utilities\PushNotificationService;
use Illuminate\Support\Facades\Mail;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

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
        $attributes['status_id'] = $attributes['status_id'] ?? status("Submitted");
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
        $order = $order->fresh()->load([
            'customer',
            'address',
            'address.town',
            'address.country',
            'address.state',
            'status',
            'order_total_orders',
            'payment_method',
            'delivery_method',
            'order_products'
        ]);

        event(new OrderStatusUpdatedEvent($order));

        return $order;
    }

    /**
     * @param Order|int $order
     * @return void
     */
    public final function processOrder(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Processing")) return;

        $order = $this->updateOrderStatus($order, status("Processing"));

        $message = new Message(
            headers: ['event' => KafkaEvent::ONLINE_PUSH],
            body: ['order' => $order->toArray(), "action" => KAFKAAction::PROCESS_ORDER],
            key: config('app.KAFKA_HEADER_KEY')
        );

        //public order to kafka
        Kafka::publish()->onTopic('orders')->withMessage($message)->send();

        //send mail to customer
        Mail::to($order->customer->user->email)->send(new NewOrderEmail($order));

        //notify the customer on his/her phone
        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "🎊 Woohoo! Your Order is on Its Way to Processing",
                "body" => "Your order #$order->order_id  has been successfully submitted! ✅💌 We’ll keep you posted and send you an email when your order status updates.",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->id])
            ->approve()
            ->send();

    }

    /**
     * @param Order|int $order
     * @return void
     */
    public final function packOrder(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Packing")) return;

        $order = $this->updateOrderStatus($order, status("Packing"));

        Mail::to($order->customer->user->email)->send(new PackedOrder($order));

        //notify the customer on his/her phone
        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "📦 Your Order is Now Being Packed!",
                "body" => "Great news! Your order #{{ $order->order_id }} is now being packed and will be on its way soon. Stay tuned for shipping updates! 🚀",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }

    /**
     * @param Order|int $order
     * @return void
     */
    public final function cancelOrder(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Cancelled")) return;

        $order = $this->updateOrderStatus($order, status("Cancelled"));

        Mail::to($order->customer->user->email)->send(new CancelledOrder($order));

        //notify the customer on his/her phone
        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "⚠️ Order Canceled",
                "body" => "Your order $order->order_id has been canceled. If you need assistance, we're here to help! 💙",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }
    /**
     * @param Order|int $order
     * @return void
     */
    public final function waitingForPaymentOrder(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }


        if($order->status_id === status("Waiting For Payment")) return;

        $order = $this->updateOrderStatus($order, status('Waiting For Payment'));

        Mail::to($order->customer->user->email)->send(new WaitingForPayment($order));

        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "💳 Waiting for Payment",
                "body" => "Your order #$order->order_id is packed and ready! We’re just waiting for your payment at the store. See you soon! 😊",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }

    /**
     * @param Order|int $order
     * @return void
     */
    public final function confirmOrderPayment(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Payment Confirmed")) return;

        $order = $this->updateOrderStatus($order, status('Payment Confirmed'));

        Mail::to($order->customer->user->email)->send(new ConfirmPayment($order));

        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "✅ Payment Confirmed!",
                "body" => "We’ve received your payment for order #{{ $order->order_id }}. Thank you! Your order will be on its way soon. 🚀",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }

    /**
     * @param Order|int $order
     * @return void
     */
    public final function orderItemChanged(Order|int $order) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        Mail::to($order->customer->user->email)->send(new OrderItemChanged($order));
        $order = $this->updateOrderStatus($order, status('Processing'));

        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "📢 Order Updated!",
                "body" => "Your order #$order->order_id has been updated. View the latest details and invoice in the app. 📲",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();

    }


    /**
     * @param Order|int $order
     * @return void
     */
    public final function dispatchedOrder(Order|int $order, int $carton = 0) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Dispatched")) return;

        $order = $this->updateOrderStatus($order, status('Dispatched'));
        $order->no_of_cartons = $carton;
        $order->save();

        Mail::to($order->customer->user->email)->send(new DispatchedOrder($order));

        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "Your Order has been Dispatched",
                "body" => "Great News! Your order #$order->order_id has just been dispatched for pick up/shipping. Your order status has now been completed",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }


    /**
     * @param Order|int $order
     * @param array $errors
     * @return void
     */
    public final function processOrderError(Order|int $order, array $errors = []) : void
    {
        if(!$order instanceof Order) {
            $order = Order::findorfail($order);
        }

        if($order->status_id === status("Processing Error")) return;
        $order = $this->updateOrderStatus($order, status('Processing Error'));


        foreach ($errors as $key=>$error) {
            $orderProduct = $order->order_products()->where('local_id', $key)->first();
            if($orderProduct) {
                $orderProduct->error = $error;
                $orderProduct->save();
            }

        }

        Mail::to($order->customer->user->email)->send(new ProcessingError($order));

        $notificationService  = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment($order->app)
            ->createNotification([
                "title" => "🚨 Order Processing Issue 🚨",
                "body" => "We hit a small snag while processing your order #$order->order_id. Our team is working on it and will update you soon!",
            ])
            ->determineCustomerTypeAndSetCustomer($order->customer)
            ->setAction(PushNotificationAction::VIEW_ORDER)
            ->setPayload(['orderId' => $order->order_id])
            ->approve()
            ->send();
    }
}
