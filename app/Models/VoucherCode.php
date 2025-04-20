<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VoucherCode
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property float $type_value
 * @property Carbon $valid_from
 * @property Carbon $valid_to
 * @property string $usage_status
 * @property int $voucher_id
 * @property int|null $app_id
 * @property string|null $customer_type
 * @property int|null $customer_id
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
 * @property CustomerType|null $customer_type_type
 * @property Status|null $status
 * @property Voucher $voucher
 *
 * @package App\Models
 */
class VoucherCode extends Model
{
	protected $table = 'voucher_codes';

	protected $casts = [
		'type_value' => 'float',
		'valid_from' => 'datetime',
		'valid_to' => 'datetime',
		'voucher_id' => 'int',
		'app_id' => 'int',
		'user_id' => 'int',
		'customer_type_id' => 'int',
		'customer_group_id' => 'int',
		'status_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'type',
		'type_value',
		'valid_from',
		'valid_to',
		'usage_status',
		'voucher_id',
		'app_id',
		'customer_type',
		'customer_id',
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

    public function customer()
    {
        return $this->morphTo();
    }

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function customer_type_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function voucher()
	{
		return $this->belongsTo(Voucher::class);
	}
}
