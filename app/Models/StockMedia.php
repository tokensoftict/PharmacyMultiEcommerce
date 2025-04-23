<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class StockMedia
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $media_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 *
 * @package App\Models
 */
class StockMedia extends Model
{
	protected $table = 'stock_media';

	protected $casts = [
		'stock_id' => 'int',
		'media_id' => 'int'
	];

	protected $fillable = [
		'stock_id',
		'media_id'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
