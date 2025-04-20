<?php
namespace App\Traits;

use App\Models\Address;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use PhpParser\Node\NullableType;

trait ApplicationUserTrait
{
    use ApplicationUserCheckoutTrait;

    /**
     * @param Address|int $address
     * @return void
     */
    public final function setDefaultAddress(Address|int $address) : void
    {
        if(! $address instanceof Address){
            $address = Address::find($address);
        }

        $this->address_id = $address->id;
        $this->update();
    }

    /**
     * @param PaymentMethod|int $paymentMethod
     * @return void
     */
    public final function setDefaultPaymentMethod(PaymentMethod|int $paymentMethod) : void
    {
        if(! $paymentMethod instanceof PaymentMethod){
            $paymentMethod = PaymentMethod::find($paymentMethod);
        }

        $this->payment_method_id = $paymentMethod->id;
        $this->update();
    }


    /**
     * @param DeliveryMethod|int $deliveryMethod
     * @return void
     */
    public final function setDefaultDeliveryMethod(DeliveryMethod|int $deliveryMethod) : void
    {
        if(! $deliveryMethod instanceof DeliveryMethod){
            $deliveryMethod = DeliveryMethod::find($deliveryMethod);
        }

        $this->delivery_method_id = $deliveryMethod->id;
        $this->update();
    }

    /**
     * @param Address $address
     * @return bool
     */
    public final function checkIfDefaultAddressIs(Address $address) : bool
    {
        return $this->address_id == $address->id;
    }


    /**
     * @return void
     */
    public final function removeDefaultAddress() : void
    {
        $this->address_id = NULL;
        $this->update();
    }

    /**
     * @return int|NullableType
     */
    public final function getDefaultAddress() : int|NullableType
    {
       return $this->address_id;
    }

    /**
     * @param string $key
     * @param array|string $value
     * @return void
     */
    public final function saveCheckoutData(string $key, array|string $value) : void
    {
        $checkOutData = $this->checkout;

        if(empty($checkOutData)){
            $checkOutData = [];
        }

        $checkOutData[$key] = $value;
        $this->checkout = $checkOutData;
        $this->update();
    }


    /**
     * @return array|bool
     */
    public final function getCheckoutDeliveryMethod() : array|bool
    {
        $checkOutData = $this->checkout;

        if(empty($checkOutData)) return false;

        if(!isset($checkOutData['deliveryMethod'])) return false;

        return $checkOutData['deliveryMethod'];
    }


    /**
     * @return array|bool
     */
    public final function getCheckoutPaymentMethod() : int|bool
    {
        $checkOutData = $this->checkout;

        if(empty($checkOutData)) return false;

        if(!isset($checkOutData['paymentMethod'])) return false;

        return $checkOutData['paymentMethod'];
    }


    /**
     * @return array|bool
     */
    public final function getCheckoutAddress() : int|bool
    {
        $checkOutData = $this->checkout;

        if(empty($checkOutData)) return false;

        if(!isset($checkOutData['deliveryAddressId'])) return false;

        return $checkOutData['deliveryAddressId'];
    }

    /**
     * @param string $value
     * @return void
     */
    public final function saveOrderTotalToRemoveOrAdd(string $value) : void
    {
        $orderTotals = $this->remove_order_total;
        if(empty($orderTotals)){
            $orderTotals = [];
        }

        if(!in_array($value, $orderTotals)){
            $orderTotals[] = (int)$value;
        } else {
           foreach($orderTotals as $key => $orderTotal){
               if($orderTotal == $value){
                   unset($orderTotals[$key]);
               }
           }
        }

        $this->remove_order_total = $orderTotals;
        $this->update();
    }

    /**
     * @return void
     */
    public final function resetOrderTotals() : void
    {
        $this->remove_order_total = "[]";
        $this->update();
    }

    /**
     * @param string|array $value
     * @return void
     */
    public final function saveCouponData(string|array $value) : void
    {
        $this->coupon_data = $value;
        $this->update();
    }


    /**
     * @return void
     */
    public final function removeCouponData() : void
    {
        $this->coupon_data = NULL;
        $this->update();
    }

    /**
     * @return bool
     */
    public final function isVoucherCode() : bool
    {
        $voucher = $this->coupon_data;
        if(is_null($voucher)) return false;

        return $voucher['discount_type'] == "Voucher";
    }

    /**
     * @return bool
     */
    public final function isCouponCode() : bool
    {
        $voucher = $this->coupon_data;
        if(is_null($voucher)) return false;

        return $voucher['discount_type'] == "Coupon";
    }

    /**
     * @return bool
     */
    public final function prepareUserAccountForNewOrder() : bool
    {
        $this->remove_order_total = [];
        $this->coupon_data = NULL;
        $this->ordertotals = NULL;
        $this->checkout = NULL;
        $this->cart = NULL;
        return $this->update();
    }


    public function routeNotificationForFcm()
    {
        return $this->device_key;
    }


    public function updateLastActivity() : void
    {
        $this->last_activity_date = now();
        $this->save();
    }

}
