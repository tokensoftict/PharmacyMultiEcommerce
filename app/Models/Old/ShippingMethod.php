<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $code
 * @property string $created_at
 * @property string $updated_at
 * @property Order[] $orders
 * @property string template_settings_value
 * @property string template_settings
 * @property string checkout_template
 */
class ShippingMethod extends Model
{

    protected $table = 'shipping_method';
    protected $connection = 'old_server_mysql';

    /**
     * @var array
     */
    protected $fillable = ['name', 'path', 'code','template_settings','template_settings_value','checkout_template', 'created_at', 'updated_at'];


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
