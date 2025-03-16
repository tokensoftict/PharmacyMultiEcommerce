<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ApplicationUserTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class SalesRepresentative
 *
 * @property int $id
 * @property int|null $user_id
 * @property bool $status
 * @property bool $invitation_status
 * @property Carbon|null $invitation_sent_date
 * @property Carbon|null $invitation_approval_date
 * @property int|null $added_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 *
 * @package App\Models
 */
class SalesRepresentative extends Model
{
	protected $table = 'sales_representatives';
    use ApplicationUserTrait, Notifiable;

	protected $casts = [
		'user_id' => 'int',
		'status' => 'bool',
		'invitation_status' => 'bool',
		'invitation_sent_date' => 'datetime',
		'invitation_approval_date' => 'datetime',
		'added_by' => 'int'
	];

	protected $fillable = [
		'user_id',
		'status',
		'invitation_status',
		'invitation_sent_date',
		'invitation_approval_date',
		'added_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function app_user()
    {
        return $this->morphOne(AppUser::class,'user_type');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'sales_representative_id');
    }

    public function wholesales_users()
    {
       return $this->hasMany(WholesalesUser::class, 'sales_representative_id');
    }
}
