<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CouponUsageHistory
 * 
 * @property int $id
 * @property string $code
 * @property Carbon $use_date
 * @property int $coupon_id
 * @property int $app_id
 * @property string $user_type_type
 * @property int $user_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App $app
 * @property Coupon $coupon
 *
 * @package App\Models
 */
class CouponUsageHistory extends Model
{
	protected $table = 'coupon_usage_histories';

	protected $casts = [
		'use_date' => 'datetime',
		'coupon_id' => 'int',
		'app_id' => 'int',
		'user_type_id' => 'int'
	];

	protected $fillable = [
		'code',
		'use_date',
		'coupon_id',
		'app_id',
		'user_type_type',
		'user_type_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function coupon()
	{
		return $this->belongsTo(Coupon::class);
	}
}
