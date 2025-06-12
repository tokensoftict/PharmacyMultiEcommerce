<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $code
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property Order[] $orders
 */
class PaymentMethod extends Model
{

    protected $connection = 'old_server_mysql';

    protected $table = 'payment_method';

    protected $fillable = ['name', 'path', 'description','code', 'created_at','template_settings_value', 'template_settings' ,'updated_at'];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function toggle()
    {
        $this->status = !$this->status;
        $this->save();
    }
}
