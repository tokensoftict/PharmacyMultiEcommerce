<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockSize
 * 
 * @property int $id
 * @property int $stock_id
 * @property float $product_size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 *
 * @package App\Models
 */
class StockSize extends Model
{
	protected $table = 'stock_sizes';

	protected $casts = [
		'stock_id' => 'int',
		'product_size' => 'float'
	];

	protected $fillable = [
		'stock_id',
		'product_size'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}
}
