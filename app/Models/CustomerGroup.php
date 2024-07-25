<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerGroup
 * 
 * @property int $id
 * @property string|null $name
 * @property bool $status
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class CustomerGroup extends Model
{
	protected $table = 'customer_groups';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'status',
		'description'
	];
}
