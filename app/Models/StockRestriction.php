<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockRestriction
 *
 * @property int $id
 * @property string $group_type
 * @property int $group_id
 * @property int $stock_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Stock $stock
 * @property User|null $user
 *
 * @package App\Models
 */
class StockRestriction extends Model
{
	protected $table = 'stock_restrictions';

	protected $casts = [
		'group_id' => 'int',
		'stock_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'group_type',
		'group_id',
		'stock_id',
		'user_id'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function group()
    {
        return $this->morphTo();
    }
}
