<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Brand
 * 
 * @property int $id
 * @property string $name
 * @property string|null $seo
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Brand extends Model
{
	protected $table = 'brands';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'seo',
		'status'
	];
}
