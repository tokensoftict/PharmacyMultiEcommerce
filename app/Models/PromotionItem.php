<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PromotionItem
 * 
 * @property int $id
 * @property int $promotion_id
 * @property int $stock_id
 * @property int $user_id
 * @property int|null $status_id
 * @property int|null $customer_group_id
 * @property int|null $customer_type_id
 * @property int|null $app_id
 * @property Carbon $from_date
 * @property Carbon $end_date
 * @property Carbon $created
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property CustomerGroup|null $customer_group
 * @property CustomerType|null $customer_type
 * @property Promotion $promotion
 * @property Status|null $status
 * @property Stock $stock
 * @property User $user
 *
 * @package App\Models
 */
class PromotionItem extends Model
{
	protected $table = 'promotion_items';

	protected $casts = [
		'promotion_id' => 'int',
		'stock_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int',
		'customer_group_id' => 'int',
		'customer_type_id' => 'int',
		'app_id' => 'int',
		'from_date' => 'datetime',
		'end_date' => 'datetime',
		'created' => 'datetime',
		'price' => 'float'
	];

	protected $fillable = [
		'promotion_id',
		'stock_id',
		'user_id',
		'status_id',
		'customer_group_id',
		'customer_type_id',
		'app_id',
		'from_date',
		'end_date',
		'created',
		'price'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function customer_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

	public function promotion()
	{
		return $this->belongsTo(Promotion::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
