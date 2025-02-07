<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coupon
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property Carbon $valid_from
 * @property Carbon $valid_to
 * @property int $noofuse
 * @property string $type
 * @property float $type_value
 * @property int|null $app_id
 * @property string|null $users_type
 * @property int|null $users_id
 * @property int|null $customer_type_id
 * @property int|null $customer_group_id
 * @property int|null $status_id
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property User|null $user
 * @property CustomerGroup|null $customer_group
 * @property CustomerType|null $customer_type
 * @property Status|null $status
 *
 * @package App\Models
 */
class Coupon extends Model
{
	protected $table = 'coupons';

	protected $casts = [
		'valid_from' => 'datetime',
		'valid_to' => 'datetime',
		'noofuse' => 'int',
		'type_value' => 'float',
		'app_id' => 'int',
		'users_id' => 'int',
		'customer_type_id' => 'int',
		'customer_group_id' => 'int',
		'status_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'valid_from',
		'valid_to',
		'noofuse',
		'type',
		'type_value',
		'app_id',
		'users_type',
		'users_id',
		'customer_type_id',
		'customer_group_id',
		'status_id',
		'created_by'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function customer_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

    public function couponUsageHistories()
    {
        return $this->hasMany(CouponUsageHistory::class);
    }
}
