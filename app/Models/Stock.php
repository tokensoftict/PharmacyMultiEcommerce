<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\StockModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * Class Stock
 *
 * @property int $id
 * @property int $local_stock_id
 * @property string|null $name
 * @property string|null $seo
 * @property string|null $description
 * @property int|null $productcategory_id
 * @property int|null $manufacturer_id
 * @property int|null $classification_id
 * @property int|null $productgroup_id
 * @property int $wholesales
 * @property int $retail
 * @property int $quantity
 * @property Carbon|null $expiry_date
 * @property int $piece
 * @property int $box
 * @property int $carton
 * @property bool $sachet
 * @property bool $admin_status
 * @property string|null $image
 * @property bool $is_wholesales
 * @property int $max
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Classification|null $classification
 * @property Manufacturer|null $manufacturer
 * @property Productcategory|null $productcategory
 * @property Productgroup|null $productgroup
 * @property User|null $user
 * @property Collection|NewStockArrival[] $new_stock_arrivals
 * @property Collection|OrderProduct[] $order_products
 * @property Collection|PromotionItem[] $promotion_items
 * @property Collection|StockRestriction[] $stock_restrictions
 * @property Collection|StockSize[] $stock_sizes
 * @property Collection|SupermarketsStockPrice[] $supermarkets_stock_prices
 * @property Collection|WholessalesStockPrice[] $wholessales_stock_prices
 * @property Collection|StockMedia[] $stock_medias
 * @property Collection|ProductCustomPrice[] $stockquantityprices
 * @property StockMedia $stock_media
 *
 * @package App\Models
 */
class Stock extends Model
{
    use StockModelTrait, Searchable;

	protected $table = 'stocks';

	protected $casts = [
		'local_stock_id' => 'int',
		'productcategory_id' => 'int',
		'manufacturer_id' => 'int',
		'classification_id' => 'int',
		'productgroup_id' => 'int',
		'wholesales' => 'int',
		'retail' => 'int',
		'quantity' => 'int',
		'expiry_date' => 'datetime',
		'piece' => 'int',
		'box' => 'int',
		'carton' => 'int',
		'sachet' => 'bool',
        'admin_status' => 'bool',
		'is_wholesales' => 'bool',
		'max' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'local_stock_id',
		'name',
		'seo',
		'description',
		'productcategory_id',
		'manufacturer_id',
		'classification_id',
		'productgroup_id',
		'wholesales',
		'retail',
		'quantity',
		'expiry_date',
		'piece',
		'box',
		'carton',
		'sachet',
		'image',
		'is_wholesales',
        'admin_status',
		'max',
		'user_id'
	];

    protected $appends = ['wholesales_status', 'retail_status'];

    protected $with = ['stockquantityprices','stock_sizes', 'classification', 'productgroup', 'manufacturer', 'promotion_items', 'stock_media.media'];


    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['image'] = $this->stock_media ? $this->stock_media->media->getFullUrl() : asset("logo/no-image.png");

        if(isset($this->wholessales_stock_prices->id)) {
            $array['wholesales'] = $this->wholessales_stock_prices->toArray();
            $array['wholesales']['price'] = money($array['wholesales']['price']);
        } else {
            $array['wholesales'] = false;
        }

        if(isset($this->supermarkets_stock_prices)) {
            $array['retail'] = $this->supermarkets_stock_prices->toArray();
            $array['retail']['price'] = money( $array['retail']['price']);
        } else {
            $array['retail'] = false;
        }

        return $array;
    }

    public function classification()
	{
		return $this->belongsTo(Classification::class);
	}

	public function manufacturer()
	{
		return $this->belongsTo(Manufacturer::class);
	}

	public function productcategory()
	{
		return $this->belongsTo(Productcategory::class);
	}

	public function productgroup()
	{
		return $this->belongsTo(Productgroup::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function new_stock_arrivals()
	{
		return $this->hasMany(NewStockArrival::class);
	}

	public function order_products()
	{
		return $this->hasMany(OrderProduct::class);
	}

	public function promotion_items()
	{
		return $this->hasMany(PromotionItem::class);
	}


	public function stock_restrictions()
	{
		return $this->hasOne(StockRestriction::class);
	}

	public function stock_sizes()
	{
		return $this->hasOne(StockSize::class);
	}

	public function supermarkets_stock_prices()
	{
		return $this->hasOne(SupermarketsStockPrice::class);
	}

	public function wholessales_stock_prices()
	{
		return $this->hasOne(WholessalesStockPrice::class);
	}

    public function stock_medias()
    {
        return $this->hasMany(StockMedia::class);
    }

    public function stock_media()
    {
        return $this->hasOne(StockMedia::class);
    }

    public function stockquantityprices()
    {
        return $this->hasMany(ProductCustomPrice::class);
    }
}
