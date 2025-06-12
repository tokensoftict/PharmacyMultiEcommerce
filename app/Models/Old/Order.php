<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property integer $customer_group_id
 * @property integer $payment_address_id
 * @property integer $shipping_address_id
 * @property integer $payment_method_id
 * @property integer $shipping_method_id
 * @property integer $order_status_id
 * @property integer $invoice_no
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $telephone
 * @property string $comment
 * @property float $total
 * @property string $ip
 * @property string $user_agent
 * @property string $prove_of_payment
 * @property string $created_at
 * @property string $updated_at
 * @property CustomerGroup $customerGroup
 * @property User $user
 * @property OrderStatus $orderStatus
 * @property Address $address
 * @property PaymentMethod $paymentMethod
 * @property Address $shippingAddress
 * @property ShippingMethod $shippingMethod
 * @property OrderProduct[] $orderProducts
 * @property OrderTotalOrder[] $orderTotalOrders
 */
class Order extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'order';

    protected $fillable = ['order_validation_error','user_type','voucher_information','cart_cache','customer_type','store','sales_rep_id','prove_of_payment','no_of_cartons','user_id','order_date','payment_gateway_response', 'checkout_data', 'ordertotals' ,'customer_group_id', 'payment_address_id', 'shipping_address_id', 'payment_method_id', 'shipping_method_id', 'order_status_id', 'invoice_no', 'firstname', 'lastname', 'email', 'telephone', 'comment', 'total', 'ip', 'user_agent', 'created_at', 'updated_at'];



    public function customerGroup()
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'payment_address_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function orderTotalOrders()
    {
        return $this->hasMany(OrderTotalOrder::class);
    }

}
