<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * 
 * @property int $id
 * @property string $order_id
 * @property int $invoice_no
 * @property string $customer_type
 * @property int $customer_id
 * @property int|null $customer_group_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $telephone
 * @property Carbon $order_date
 * @property int $payment_method_id
 * @property int $delivery_method_id
 * @property string|null $comment
 * @property float $total
 * @property int|null $status_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int $payment_address_id
 * @property int $shipping_address_id
 * @property string|null $payment_gateway_response
 * @property string|null $checkout_data
 * @property string|null $ordertotals
 * @property int|null $no_of_cartons
 * @property string|null $prove_of_payment
 * @property array|null $order_validation_error
 * @property int|null $app_id
 * @property int|null $sales_representative_id
 * @property array|null $coupon_information
 * @property array|null $voucher_information
 * @property array|null $cart_cache
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property CustomerGroup|null $customer_group
 * @property DeliveryMethod $delivery_method
 * @property Address $address
 * @property PaymentMethod $payment_method
 * @property SalesRepresentative|null $sales_representative
 * @property Status|null $status
 * @property Collection|OrderProduct[] $order_products
 * @property Collection|OrderTotalOrder[] $order_total_orders
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';

	protected $casts = [
		'invoice_no' => 'int',
		'customer_id' => 'int',
		'customer_group_id' => 'int',
		'order_date' => 'datetime',
		'payment_method_id' => 'int',
		'delivery_method_id' => 'int',
		'total' => 'float',
		'status_id' => 'int',
		'payment_address_id' => 'int',
		'shipping_address_id' => 'int',
		'no_of_cartons' => 'int',
		'order_validation_error' => 'json',
		'app_id' => 'int',
		'sales_representative_id' => 'int',
		'coupon_information' => 'json',
		'voucher_information' => 'json',
		'cart_cache' => 'json',
        'checkout_data' => 'json',
        'ordertotals' => 'json'
	];

	protected $fillable = [
		'order_id',
		'invoice_no',
		'customer_type',
		'customer_id',
		'customer_group_id',
		'firstname',
		'lastname',
		'email',
		'telephone',
		'order_date',
		'payment_method_id',
		'delivery_method_id',
		'comment',
		'total',
		'status_id',
		'ip',
		'user_agent',
		'payment_address_id',
		'shipping_address_id',
		'payment_gateway_response',
		'checkout_data',
		'ordertotals',
		'no_of_cartons',
		'prove_of_payment',
		'order_validation_error',
		'app_id',
		'sales_representative_id',
		'coupon_information',
		'voucher_information',
		'cart_cache'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

    public function customer()
    {
        return $this->morphTo();
    }

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function delivery_method()
	{
		return $this->belongsTo(DeliveryMethod::class);
	}

	public function address()
	{
		return $this->belongsTo(Address::class, 'shipping_address_id');
	}

	public function payment_method()
	{
		return $this->belongsTo(PaymentMethod::class);
	}

	public function sales_representative()
	{
		return $this->belongsTo(SalesRepresentative::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function order_products()
	{
		return $this->hasMany(OrderProduct::class);
	}

	public function order_total_orders()
	{
		return $this->hasMany(OrderTotalOrder::class);
	}
}
