<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
class Manufacturer extends Model implements HasMedia
{
    use InteractsWithMedia;

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


    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }


    public function getImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl() ?: ($this->getRawOriginal('image') ?? asset('logo/no-image.png'));
    }


}
