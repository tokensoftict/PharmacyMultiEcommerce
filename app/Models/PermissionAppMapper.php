<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionAppMapper
 * 
 * @property int $id
 * @property int $permission_id
 * @property int $app_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App $app
 * @property Permission $permission
 *
 * @package App\Models
 */
class PermissionAppMapper extends Model
{
	protected $table = 'permission_app_mappers';

	protected $casts = [
		'permission_id' => 'int',
		'app_id' => 'int'
	];

	protected $fillable = [
		'permission_id',
		'app_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}
}
