<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DependentProduct
 *
 * @property int $id
 * @property int $stock_id
 * @property int $dependent_local_stock_id
 * @property bool $parent
 * @property bool $child
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property Stock $stock
 *
 * @package App\Models
 */
class DependentProduct extends Model
{
    protected $table = 'dependent_products';

    protected $casts = [
        'stock_id' => 'int',
        'dependent_local_stock_id' => 'int',
        'parent' => 'int',
        'child' => 'int',
    ];

    protected $fillable = [
        'stock_id',
        'dependent_local_stock_id',
        'parent',
        'child',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function dependent_stock()
    {
        return $this->belongsTo(Stock::class, 'dependent_local_stock_id', 'local_stock_id');
    }
}
