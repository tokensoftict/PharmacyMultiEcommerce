<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotificationCustomer
 * 
 * @property int $id
 * @property int $push_notification_id
 * @property string $customer_type
 * @property int $customer_id
 * @property int|null $status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PushNotification $push_notification
 * @property Status|null $status
 *
 * @package App\Models
 */
class PushNotificationCustomer extends Model
{
	protected $table = 'push_notification_customers';

	protected $casts = [
		'push_notification_id' => 'int',
		'customer_id' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'push_notification_id',
		'customer_type',
		'customer_id',
		'status_id'
	];

	public function push_notification()
	{
		return $this->belongsTo(PushNotification::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

    public function customer()
    {
        return $this->morphTo();
    }
}
