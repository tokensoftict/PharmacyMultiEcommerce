<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderTotal
 * 
 * @property int $id
 * @property string $title
 * @property string $order_total_type
 * @property string $code
 * @property int $status
 * @property float $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OrderTotal extends Model
{
	protected $table = 'order_totals';

	protected $casts = [
		'status' => 'int',
		'value' => 'float'
	];

	protected $fillable = [
		'title',
		'order_total_type',
		'code',
		'status',
		'value'
	];
}
