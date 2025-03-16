<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModuleAppMapper
 * 
 * @property int $id
 * @property int $module_id
 * @property int $app_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App $app
 * @property Module $module
 *
 * @package App\Models
 */
class ModuleAppMapper extends Model
{
	protected $table = 'module_app_mappers';

	protected $casts = [
		'module_id' => 'int',
		'app_id' => 'int'
	];

	protected $fillable = [
		'module_id',
		'app_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function module()
	{
		return $this->belongsTo(Module::class);
	}
}
