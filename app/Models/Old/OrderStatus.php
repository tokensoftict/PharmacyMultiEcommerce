<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Order[] $orders
 */
class OrderStatus extends Model
{
    protected $connection = 'old_server_mysql';
    protected $table = 'order_status';

    protected $fillable = ['name', 'created_at', 'updated_at'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
