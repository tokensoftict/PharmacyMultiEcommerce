<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Productcategory
 * 
 * @property int $id
 * @property string $name
 * @property string|null $seo
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|CustomerSearchHistory[] $customer_search_histories
 * @property Collection|Stock[] $stocks
 *
 * @package App\Models
 */
class Productcategory extends Model
{
	protected $table = 'productcategories';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'seo',
		'status'
	];

	public function customer_search_histories()
	{
		return $this->hasMany(CustomerSearchHistory::class);
	}

	public function stocks()
	{
		return $this->hasMany(Stock::class);
	}
}
