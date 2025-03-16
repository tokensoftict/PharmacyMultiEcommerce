<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * 
 * @property int $id
 * @property string $name
 * @property int $module_id
 * @property string $label
 * @property bool $visibility
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Module $module
 * @property Collection|App[] $apps
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class Permission extends Model
{
	protected $table = 'permissions';

	protected $casts = [
		'module_id' => 'int',
		'visibility' => 'bool'
	];

	protected $fillable = [
		'name',
		'module_id',
		'label',
		'visibility',
		'guard_name'
	];

	public function module()
	{
		return $this->belongsTo(Module::class);
	}


	public function apps()
	{
		return $this->belongsToMany(App::class, 'permission_app_mappers')
					->withPivot('id')
					->withTimestamps();
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_has_permissions');
	}
}
