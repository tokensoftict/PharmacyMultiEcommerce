<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductBanner
 * 
 * @property int $id
 * @property string $title
 * @property string $image
 * @property bool $status
 * @property int|null $stock_id
 * @property int|null $classification_id
 * @property int|null $productgroup_id
 * @property int|null $manufacturer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Classification|null $classification
 * @property Manufacturer|null $manufacturer
 * @property Productgroup|null $productgroup
 *
 * @package App\Models
 */
class ProductBanner extends Model
{
	protected $table = 'product_banners';

	protected $casts = [
		'status' => 'bool',
		'stock_id' => 'int',
		'classification_id' => 'int',
		'productgroup_id' => 'int',
		'manufacturer_id' => 'int'
	];

	protected $fillable = [
		'title',
		'image',
		'status',
		'stock_id',
		'classification_id',
		'productgroup_id',
		'manufacturer_id'
	];

	public function classification()
	{
		return $this->belongsTo(Classification::class);
	}

	public function manufacturer()
	{
		return $this->belongsTo(Manufacturer::class);
	}

	public function productgroup()
	{
		return $this->belongsTo(Productgroup::class);
	}
}
