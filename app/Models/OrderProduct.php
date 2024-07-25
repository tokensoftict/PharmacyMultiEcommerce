<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderProduct
 * 
 * @property int $id
 * @property string $order_product_id
 * @property int $order_id
 * @property int $stock_id
 * @property int $local_id
 * @property string $name
 * @property string $model
 * @property int $quantity
 * @property float $price
 * @property float $total
 * @property float $tax
 * @property int $reward
 * @property int|null $app_id
 * @property int|null $sales_representative_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property Order $order
 * @property SalesRepresentative|null $sales_representative
 * @property Stock $stock
 *
 * @package App\Models
 */
class OrderProduct extends Model
{
	protected $table = 'order_products';

	protected $casts = [
		'order_id' => 'int',
		'stock_id' => 'int',
		'local_id' => 'int',
		'quantity' => 'int',
		'price' => 'float',
		'total' => 'float',
		'tax' => 'float',
		'reward' => 'int',
		'app_id' => 'int',
		'sales_representative_id' => 'int'
	];

	protected $fillable = [
		'order_product_id',
		'order_id',
		'stock_id',
		'local_id',
		'name',
		'model',
		'quantity',
		'price',
		'total',
		'tax',
		'reward',
		'app_id',
		'sales_representative_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function sales_representative()
	{
		return $this->belongsTo(SalesRepresentative::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}
}
