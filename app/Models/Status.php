<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Status
 * 
 * @property int $id
 * @property string $name
 * @property string $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Coupon[] $coupons
 *
 * @package App\Models
 */
class Status extends Model
{
	protected $table = 'statuses';

	protected $fillable = [
		'name',
		'label'
	];

	public function coupons()
	{
		return $this->hasMany(Coupon::class);
	}
}
