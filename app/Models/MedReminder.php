<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedReminder
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $stock_id
 * @property string|null $drug_name
 * @property float $dosage
 * @property float $total_dosage_in_package
 * @property float $total_dosage_taken
 * @property array $normal_schedules
 * @property string $type
 * @property bool $use_interval
 * @property int|null $interval
 * @property string|null $every
 * @property string|null $dosage_form
 * @property Carbon|null $start_date_time
 * @property Carbon $date_create
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Stock|null $stock
 * @property User $user
 * @property Collection|MedReminderSchedule[] $med_reminder_schedules
 *
 * @package App\Models
 */
class MedReminder extends Model
{
	protected $table = 'med_reminders';

	protected $casts = [
		'user_id' => 'int',
		'stock_id' => 'int',
		'dosage' => 'float',
		'total_dosage_in_package' => 'float',
		'total_dosage_taken' => 'float',
		'normal_schedules' => 'json',
		'use_interval' => 'bool',
		'interval' => 'int',
		'start_date_time' => 'datetime',
		'date_create' => 'datetime',
	];

	protected $fillable = [
		'user_id',
		'stock_id',
		'drug_name',
		'dosage',
		'total_dosage_in_package',
		'total_dosage_taken',
		'normal_schedules',
		'type',
		'use_interval',
		'interval',
		'every',
		'start_date_time',
		'date_create',
		'notes',
        'dosage_form',
        "discount_percentage",
        "discount_generated_date",
        "discount_expiry_date",
        "is_discount_generated"
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function med_reminder_schedules()
	{
		return $this->hasMany(MedReminderSchedule::class);
	}
}
