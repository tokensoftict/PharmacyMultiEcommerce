<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $order_total_id
 * @property integer $order_id
 * @property float $value
 * @property string $created_at
 * @property string $updated_at
 * @property Order $order
 * @property OrderTotal $orderTotal
 */
class OrderTotalOrder extends Model
{

    protected $table = 'order_total_order';
    protected $connection = 'old_server_mysql';
    protected $keyType = 'integer';

    protected $fillable = ['order_total_id','name' ,'order_id','title' ,'value', 'created_at', 'updated_at'];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderTotal()
    {
        return $this->belongsTo(OrderTotal::class);
    }
}
