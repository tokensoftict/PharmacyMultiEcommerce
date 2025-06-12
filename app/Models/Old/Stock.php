<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $category_id
 * @property integer $manufacturer_id
 * @property integer $classification_id
 * @property integer $group_id
 * @property integer $brand_id
 * @property string $name
 * @property string $retail_name
 * @property string $description
 * @property string $code
 * @property string $expiry_date
 * @property float $price
 * @property float $retail_price
 * @property integer $quantity
 * @property int $featured
 * @property integer $special_offer
 * @property integer $local_stock_id
 * @property int $box
 * @property int $cartoon
 * @property boolean $sachet
 * @property boolean $status
 * @property boolean $local_sync
 * @property int $promo_restricted
 * @property string $created_at
 * @property string $updated_at
 */
class Stock extends Model
{
    protected $connection = 'old_server_mysql';
    protected $table = 'stock';

    protected $keyType = 'integer';

    protected $fillable = ['category_id','seo' ,'retail_status','manufacturer_id','image','retail_special_offer','retail_promo_restricted','retail_featured','retail_name','retail_price','retail_quantity','retail_qty','is_wholesales', 'classification_id', 'group_id', 'brand_id', 'name', 'description', 'code', 'expiry_date', 'price', 'quantity', 'featured', 'special_offer', 'local_stock_id', 'box', 'cartoon', 'sachet', 'status', 'admin_status' ,'local_sync', 'promo_restricted', 'created_at', 'updated_at'];

}
