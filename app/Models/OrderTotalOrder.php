<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderTotalOrder
 * 
 * @property int $id
 * @property int|null $order_total_id
 * @property int $order_id
 * @property string|null $name
 * @property float|null $value
 * @property int|null $discount_id
 * @property string|null $discount_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Order $order
 * @property OrderTotal|null $order_total
 *
 * @package App\Models
 */
class OrderTotalOrder extends Model
{
	protected $table = 'order_total_orders';

	protected $casts = [
		'order_total_id' => 'int',
		'order_id' => 'int',
		'value' => 'float',
        'discount_id' => 'int'
	];

	protected $fillable = [
		'order_total_id',
		'order_id',
		'name',
		'value',
        'discount_id',
        'discount_type'
	];

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function order_total()
	{
		return $this->belongsTo(OrderTotal::class);
	}
}
