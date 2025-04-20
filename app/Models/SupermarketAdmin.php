<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ApplicationUserTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SupermarketAdmin
 * 
 * @property int $id
 * @property int|null $user_id
 * @property bool $status
 * @property bool $invitation_status
 * @property Carbon|null $invitation_sent_date
 * @property Carbon|null $invitation_approval_date
 * @property string|null $token
 * @property string|null $code
 * @property int|null $added_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $last_activity_date
 *
 * @property User|null $user
 *
 * @package App\Models
 */
class SupermarketAdmin extends Model
{
    use ApplicationUserTrait;

	protected $table = 'supermarket_admins';

    public $modelTable = 'supermarket_admins';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'bool',
		'invitation_status' => 'bool',
		'invitation_sent_date' => 'datetime',
		'invitation_approval_date' => 'datetime',
		'added_by' => 'int',
        'last_activity_date' => 'datetime',
	];

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'user_id',
		'status',
		'invitation_status',
		'invitation_sent_date',
		'invitation_approval_date',
		'token',
		'code',
		'added_by',
        'last_activity_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function app_user()
    {
        return $this->morphOne(AppUser::class,'user_type');
    }
}
