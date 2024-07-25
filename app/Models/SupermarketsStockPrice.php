<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SupermarketsStockPrice
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $app_id
 * @property bool $status
 * @property int $quantity
 * @property bool $featured
 * @property bool $special_offer
 * @property float $price
 * @property Carbon|null $expiry_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property Stock $stock
 *
 * @package App\Models
 */
class SupermarketsStockPrice extends Model
{
	protected $table = 'supermarkets_stock_prices';

	protected $casts = [
		'stock_id' => 'int',
		'app_id' => 'int',
		'status' => 'bool',
		'quantity' => 'int',
		'featured' => 'bool',
		'special_offer' => 'bool',
		'price' => 'float',
		'expiry_date' => 'datetime'
	];

	protected $fillable = [
		'stock_id',
		'app_id',
		'status',
		'quantity',
		'featured',
		'special_offer',
		'price',
		'expiry_date'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}
}
