<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerType
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class CustomerType extends Model
{
	protected $table = 'customer_types';

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'name',
		'status'
	];

    public function group()
    {
        return $this->morphOne(StockRestriction::class,'group');
    }
}
