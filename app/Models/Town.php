<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Town
 * 
 * @property int $id
 * @property int $state_id
 * @property int $local_govt_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property LocalGovt $local_govt
 * @property State $state
 *
 * @package App\Models
 */
class Town extends Model
{
	protected $table = 'towns';

	protected $casts = [
		'state_id' => 'int',
		'local_govt_id' => 'int'
	];

	protected $fillable = [
		'state_id',
		'local_govt_id',
		'name'
	];

	public function local_govt()
	{
		return $this->belongsTo(LocalGovt::class);
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}
}
