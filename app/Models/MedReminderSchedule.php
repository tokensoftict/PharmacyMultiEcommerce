<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedReminderSchedule
 * 
 * @property int $id
 * @property int $med_reminder_id
 * @property Carbon $scheduled_at
 * @property string|null $title
 * @property string $status
 * @property Carbon|null $snoozed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MedReminder $med_reminder
 *
 * @package App\Models
 */
class MedReminderSchedule extends Model
{
	protected $table = 'med_reminder_schedules';

	protected $casts = [
		'med_reminder_id' => 'int',
		'scheduled_at' => 'datetime',
		'snoozed_at' => 'datetime'
	];

	protected $fillable = [
		'med_reminder_id',
		'scheduled_at',
		'title',
		'status',
		'snoozed_at'
	];

	public function med_reminder()
	{
		return $this->belongsTo(MedReminder::class);
	}
}
