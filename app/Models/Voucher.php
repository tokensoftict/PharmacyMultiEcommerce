<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Voucher
 * 
 * @property int $id
 * @property string $name
 * @property Carbon $valid_from
 * @property Carbon $valid_to
 * @property string $type
 * @property float $type_value
 * @property int|null $app_id
 * @property int $noofvoucher
 * @property string|null $user_type
 * @property int|null $user_id
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
 * @property Collection|VoucherCode[] $voucher_codes
 *
 * @package App\Models
 */
class Voucher extends Model
{
	protected $table = 'vouchers';

	protected $casts = [
		'valid_from' => 'datetime',
		'valid_to' => 'datetime',
		'type_value' => 'float',
		'app_id' => 'int',
		'noofvoucher' => 'int',
		'user_id' => 'int',
		'customer_type_id' => 'int',
		'customer_group_id' => 'int',
		'status_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'name',
		'valid_from',
		'valid_to',
		'type',
		'type_value',
		'app_id',
		'noofvoucher',
		'user_type',
		'user_id',
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

	public function voucher_codes()
	{
		return $this->hasMany(VoucherCode::class);
	}
}
