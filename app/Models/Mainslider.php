<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mainslider
 * 
 * @property int $id
 * @property string $title
 * @property string $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Mainslider extends Model
{
	protected $table = 'mainsliders';

	protected $fillable = [
		'title',
		'image'
	];
}
