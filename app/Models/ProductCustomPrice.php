<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCustomPrice
 * 
 * @property int $id
 * @property int $stock_id
 * @property float $price
 * @property float $wholesale_price
 * @property int $min_qty
 * @property int $max_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 *
 * @package App\Models
 */
class ProductCustomPrice extends Model
{
	protected $table = 'product_custom_prices';

	protected $casts = [
        'stock_id' => 'int',
        'user_id' => 'int',
        'price' => 'float',
        'wholesale_price' => 'float',
        'min_qty' => 'int',
        'max_qty' => 'int'
	];

	protected $fillable = [
		'stock_id',
		'price',
        'wholesale_price',
		'min_qty',
		'max_qty'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}
}
