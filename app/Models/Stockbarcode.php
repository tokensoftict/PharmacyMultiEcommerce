<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Stockbarcode
 * 
 * @property int $id
 * @property string $barcode
 * @property int $stock_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property Stock $stock
 *
 * @package App\Models
 */
class Stockbarcode extends Model
{
    protected $table = 'stockbarcodes';

    protected $fillable = [
        'barcode',
        'stock_id'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
