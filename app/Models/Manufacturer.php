<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Manufacturer
 * 
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string $description
 * @property string|null $seo
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Manufacturer extends Model
{
	protected $table = 'manufacturers';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'image',
		'description',
		'seo',
		'status'
	];
}
