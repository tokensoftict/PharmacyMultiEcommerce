<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LocalGovt
 * 
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property State $state
 * @property Collection|Town[] $towns
 *
 * @package App\Models
 */
class LocalGovt extends Model
{
	protected $table = 'local_govts';

	protected $casts = [
		'state_id' => 'int'
	];

	protected $fillable = [
		'state_id',
		'name'
	];

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function towns()
	{
		return $this->hasMany(Town::class);
	}
}
