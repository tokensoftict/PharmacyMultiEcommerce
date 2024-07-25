<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class State
 * 
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Country $country
 *
 * @package App\Models
 */
class State extends Model
{
	protected $table = 'states';

	protected $casts = [
		'country_id' => 'int'
	];

	protected $fillable = [
		'name',
		'country_id'
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}
}
