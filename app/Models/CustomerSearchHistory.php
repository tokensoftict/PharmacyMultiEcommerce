<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerSearchHistory
 * 
 * @property int $id
 * @property string|null $customer_type
 * @property int|null $customer_id
 * @property string $keyword
 * @property int|null $productcategory_id
 * @property string $ip
 * @property Carbon $date_added
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Productcategory|null $productcategory
 *
 * @package App\Models
 */
class CustomerSearchHistory extends Model
{
	protected $table = 'customer_search_histories';

	protected $casts = [
		'customer_id' => 'int',
		'productcategory_id' => 'int',
		'date_added' => 'datetime'
	];

	protected $fillable = [
		'customer_type',
		'customer_id',
		'keyword',
		'productcategory_id',
		'ip',
		'date_added'
	];

	public function productcategory()
	{
		return $this->belongsTo(Productcategory::class);
	}
}
