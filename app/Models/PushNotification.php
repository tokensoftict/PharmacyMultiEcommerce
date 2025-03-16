<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotification
 * 
 * @property int $id
 * @property string $title
 * @property string $body
 * @property array|null $payload
 * @property array|null $device_ids
 * @property int $app_id
 * @property int|null $customer_type_id
 * @property int|null $customer_group_id
 * @property int|null $no_of_device
 * @property string $action
 * @property string $type
 * @property int $total_view
 * @property int $total_sent
 * @property string $status
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App $app
 * @property CustomerGroup|null $customer_group
 * @property CustomerType|null $customer_type
 * @property User $user
 * @property Collection|PushNotificationCustomer[] $push_notification_customers
 * @property Collection|Stock[] $stocks
 *
 * @package App\Models
 */
class PushNotification extends Model
{
	protected $table = 'push_notifications';

	protected $casts = [
		'payload' => 'json',
		'device_ids' => 'json',
		'app_id' => 'int',
		'customer_type_id' => 'int',
		'customer_group_id' => 'int',
		'no_of_device' => 'int',
		'total_view' => 'int',
		'total_sent' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'title',
		'body',
		'payload',
		'device_ids',
		'app_id',
		'customer_type_id',
		'customer_group_id',
		'no_of_device',
		'action',
		'type',
		'total_view',
		'total_sent',
		'status',
		'user_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function customer_group()
	{
		return $this->belongsTo(CustomerGroup::class);
	}

	public function customer_type()
	{
		return $this->belongsTo(CustomerType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function push_notification_customers()
	{
		return $this->hasMany(PushNotificationCustomer::class);
	}

	public function stocks()
	{
		return $this->hasMany(PushNotificationStock::class);
	}
}
