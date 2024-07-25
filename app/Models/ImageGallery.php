<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImageGallery
 * 
 * @property int $id
 * @property string $title
 * @property string $image
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ImageGallery extends Model
{
	protected $table = 'image_galleries';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'title',
		'image',
		'status'
	];
}
