<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ApplicationUserTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class SupermarketUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $address_id
 * @property bool $status
 * @property int|null $customer_group_id
 * @property int|null $customer_type_id
 * @property string|null $device_key
 * @property int|null $customer_local_id
 * @property string|null $phone
 * @property array|null $cart
 * @property array|null $wishlist
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property CustomerGroup|null $customer_group
 * @property CustomerType|null $customer_type
 * @property User|null $user
 *
 * @package App\Models
 */
class SupermarketUser extends Model
{
    use ApplicationUserTrait, Notifiable;

	protected $table = 'supermarket_users';

	protected $casts = [
		'user_id' => 'int',
		'address_id' => 'int',
        'payment_method_id' => 'int',
        'delivery_method_id' => 'int',
		'status' => 'bool',
		'customer_group_id' => 'int',
		'customer_type_id' => 'int',
		'customer_local_id' => 'int',
		'cart' => 'json',
		'wishlist' => 'json',
        'checkout' => 'json',
        'ordertotals' => 'json',
        'coupon_data' => 'json',
        'remove_order_total' => 'json',
	];

	protected $fillable = [
		'user_id',
		'address_id',
        'payment_method_id',
        'delivery_method_id',
		'status',
		'customer_group_id',
		'customer_type_id',
		'device_key',
		'customer_local_id',
		'phone',
		'cart',
		'wishlist',
        'checkout',
        'ordertotals',
        'coupon_data',
        'remove_order_total'
	];

    protected $with = ['customer_group', 'customer_type'];

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function customer_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

    public function order()
    {
        return $this->morphMany(Order::class, 'customer');
    }

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function app_user()
    {
        return $this->morphOne(AppUser::class,'user_type');
    }
}
