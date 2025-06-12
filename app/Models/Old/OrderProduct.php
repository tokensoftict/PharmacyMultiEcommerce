<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $order_id
 * @property integer $stock_id
 * @property string $name
 * @property string $model
 * @property int $quantity
 * @property float $price
 * @property float $total
 * @property float $tax
 * @property int $reward
 * @property string $created_at
 * @property string $updated_at
 * @property Order $order
 * @property Stock $stock
 */
class OrderProduct extends Model
{
    protected $connection = 'old_server_mysql';
    protected $table = 'order_product';


    protected $keyType = 'integer';

    protected $fillable = ['local_id','order_id', 'store','sales_rep_id' ,'stock_id', 'name', 'model', 'quantity', 'price', 'total', 'tax', 'reward', 'created_at', 'updated_at'];

    protected $appends = ['localid'];

    public function getLocalidAttribute(){
        if($this->stock()->exists()){
            return $this->stock->local_stock_id;
        }
        return 0;
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

}
