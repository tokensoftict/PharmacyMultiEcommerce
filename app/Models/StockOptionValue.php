<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StockOptionValue
 *
 * @property int $id
 * @property int $stock_id
 * @property string $option_name
 * @property string $option_type
 * @property int $option_id
 * @property array $options
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property Stock $stock
 *
 * @package App\Models
 */
class StockOptionValue extends Model
{
    protected $table = 'stock_option_values';

    protected $casts = [
        'stock_id' => 'int',
        'option_id' => 'int',
        'options' => 'json',
    ];

    protected $fillable = [
        'stock_id',
        'option_name',
        'option_type',
        'option_id',
        'options',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
