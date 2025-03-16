<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class App
 * 
 * @property int $id
 * @property string $name
 * @property int|null $model_id
 * @property string|null $description
 * @property string|null $logo
 * @property bool $show
 * @property string $domain
 * @property string $link
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|User[] $users
 * @property Collection|CouponUsageHistory[] $coupon_usage_histories
 * @property Collection|Coupon[] $coupons
 * @property Collection|DeliveryMethod[] $delivery_methods
 * @property Collection|NewStockArrival[] $new_stock_arrivals
 * @property Collection|OrderProduct[] $order_products
 * @property Collection|Order[] $orders
 * @property Collection|PaymentMethod[] $payment_methods
 * @property Collection|PromotionItem[] $promotion_items
 * @property Collection|Promotion[] $promotions
 * @property Collection|PushNotificationStock[] $push_notification_stocks
 * @property Collection|PushNotification[] $push_notifications
 * @property Collection|SupermarketsStockPrice[] $supermarkets_stock_prices
 * @property Collection|VoucherCode[] $voucher_codes
 * @property Collection|Voucher[] $vouchers
 * @property Collection|WholessalesStockPrice[] $wholessales_stock_prices
 *
 * @package App\Models
 */
class App extends Model
{
	protected $table = 'apps';

	protected $casts = [
		'model_id' => 'int',
		'show' => 'bool'
	];

	protected $fillable = [
		'name',
		'model_id',
		'description',
		'logo',
		'show',
		'domain',
		'link',
		'type'
	];

	public function users()
	{
		return $this->belongsToMany(User::class, 'app_users')
					->withPivot('id', 'domain', 'user_type_type', 'user_type_id')
					->withTimestamps();
	}

	public function coupon_usage_histories()
	{
		return $this->hasMany(CouponUsageHistory::class);
	}

	public function coupons()
	{
		return $this->hasMany(Coupon::class);
	}

	public function delivery_methods()
	{
		return $this->hasMany(DeliveryMethod::class);
	}

	public function new_stock_arrivals()
	{
		return $this->hasMany(NewStockArrival::class);
	}

	public function order_products()
	{
		return $this->hasMany(OrderProduct::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function payment_methods()
	{
		return $this->hasMany(PaymentMethod::class);
	}

	public function promotion_items()
	{
		return $this->hasMany(PromotionItem::class);
	}

	public function promotions()
	{
		return $this->hasMany(Promotion::class);
	}

	public function push_notification_stocks()
	{
		return $this->hasMany(PushNotificationStock::class);
	}

	public function push_notifications()
	{
		return $this->hasMany(PushNotification::class);
	}

	public function supermarkets_stock_prices()
	{
		return $this->hasMany(SupermarketsStockPrice::class);
	}

	public function voucher_codes()
	{
		return $this->hasMany(VoucherCode::class);
	}

	public function vouchers()
	{
		return $this->hasMany(Voucher::class);
	}

	public function wholessales_stock_prices()
	{
		return $this->hasMany(WholessalesStockPrice::class);
	}
}
