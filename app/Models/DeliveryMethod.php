<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Classes\ApplicationEnvironment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryMethod
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $app_id
 * @property string $path
 * @property string $code
 * @property string|null $template_settings
 * @property array|null $template_settings_value
 * @property string|null $checkout_template
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property Collection|Order[] $orders
 *
 * @package App\Models
 */
class DeliveryMethod extends Model
{
	protected $table = 'delivery_methods';

	protected $casts = [
		'app_id' => 'int',
		'template_settings_value' => 'json',
		'status' => 'int',
		'free_delivery_until' => 'datetime'
	];

	protected $fillable = [
		'name',
		'description',
		'app_id',
		'path',
		'code',
		'template_settings',
		'template_settings_value',
		'checkout_template',
		'status',
		'free_delivery_until'
	];

	public function isFreeDeliveryActive()
	{
		return $this->free_delivery_until && $this->free_delivery_until->isFuture();
	}

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

    public function isDefault()
    {
        if(!isset( ApplicationEnvironment::getApplicationRelatedModel()?->delivery_method_id)) return false;
        return $this->id  === (ApplicationEnvironment::getApplicationRelatedModel()?->delivery_method_id ?? 0);
    }
}
