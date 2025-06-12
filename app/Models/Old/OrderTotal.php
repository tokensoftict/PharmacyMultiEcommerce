<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $title
 * @property string $code
 * @property int $status
 * @property string $order_total_type
 * @property float $value
 * @property OrderTotalOrder[] $orderTotalOrders
 */
class OrderTotal extends Model
{
    protected $table = 'order_total';
    protected $connection = 'old_server_mysql';

    protected $fillable = ['title', 'code', 'status', 'order_total_type', 'value'];

    public static $fields = ['title', 'code', 'status', 'order_total_type', 'value'];

    public static $validation = [
        "title"=>"required",
        "code"=>"required",
        "order_total_type"=>"required",
        "value"=>"required"
    ];

    public function orderTotalOrders()
    {
        return $this->hasMany(OrderTotalOrder::class);
    }

}
