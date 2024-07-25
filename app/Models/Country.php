<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $phonecode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|State[] $states
 *
 * @package App\Models
 */
class Country extends Model
{
	protected $table = 'countries';

	protected $fillable = [
		'code',
		'name',
		'phonecode'
	];

	public function states()
	{
		return $this->hasMany(State::class);
	}
}
