<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserStockPromotion
 * 
 * @property int $id
 * @property int $stock_id
 * @property int $user_id
 * @property int|null $status_id
 * @property int|null $app_id
 * @property Carbon $from_date
 * @property Carbon $end_date
 * @property Carbon $created
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property Status|null $status
 * @property Stock $stock
 * @property User $user
 *
 * @package App\Models
 */
class UserStockPromotion extends Model
{
	protected $table = 'user_stock_promotion';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int',
		'app_id' => 'int',
		'from_date' => 'datetime',
		'end_date' => 'datetime',
		'created' => 'datetime',
		'price' => 'float'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'status_id',
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
