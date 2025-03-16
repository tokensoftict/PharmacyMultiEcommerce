<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotificationStock
 * 
 * @property int $id
 * @property int $push_notification_id
 * @property int $stock_id
 * @property int|null $app_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property PushNotification $push_notification
 * @property Stock $stock
 *
 * @package App\Models
 */
class PushNotificationStock extends Model
{
	protected $table = 'push_notification_stocks';

	protected $casts = [
		'push_notification_id' => 'int',
		'stock_id' => 'int',
		'app_id' => 'int'
	];

	protected $fillable = [
		'push_notification_id',
		'stock_id',
		'app_id'
	];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function push_notification()
	{
		return $this->belongsTo(PushNotification::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}
}
