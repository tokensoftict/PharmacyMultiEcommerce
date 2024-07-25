<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
		'status' => 'int'
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
		'status'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}
