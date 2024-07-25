<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotification
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property array|null $payload
 * @property string|null $device_ids
 * @property int $app_id
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
 * @property User $user
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

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
