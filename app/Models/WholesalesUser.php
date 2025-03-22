<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ApplicationUserTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class WholesalesUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $address_id
 * @property bool $status
 * @property int|null $customer_group_id
 * @property int|null $sales_representative_id
 * @property string|null $business_name
 * @property int|null $customer_type_id
 * @property string|null $device_key
 * @property string|null $cac_document
 * @property string|null $premises_licence
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
class WholesalesUser extends Model implements HasMedia
{
    use ApplicationUserTrait, Notifiable, interactsWithMedia;

	protected $table = 'wholesales_users';

	protected $casts = [
		'user_id' => 'int',
		'address_id' => 'int',
		'status' => 'bool',
		'customer_group_id' => 'int',
		'customer_type_id' => 'int',
		'customer_local_id' => 'int',
        'sales_representative_id' => 'int',
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
		'business_name',
		'customer_type_id',
		'device_key',
		'cac_document',
		'premises_licence',
		'customer_local_id',
		'phone',
		'cart',
		'wishlist',
        'checkout',
        'ordertotals',
        'coupon_data',
        'remove_order_total',
        'sales_representative_id'
	];

    protected $with = ['customer_group', 'customer_type'];

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

    public function order()
    {
        return $this->morphMany(Order::class, 'customer');
    }

	public function customer_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function app_user()
    {
        return $this->morphOne(AppUser::class,'user_type');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
