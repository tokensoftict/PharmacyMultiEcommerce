<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $app_id
 * @property string $domain
 * @property string $user_type_type
 * @property int $user_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property App $app
 * @property User|null $user
 * @property Collection|Address[] $addresses
 * @property Collection|PushNotification[] $push_notifications
 *
 * @package App\Models
 */
class AppUser extends Model
{
	protected $table = 'app_users';

	protected $casts = [
		'user_id' => 'int',
		'app_id' => 'int',
		'user_type_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'app_id',
		'domain',
		'user_type_type',
		'user_type_id'
	];

    protected $with = ['app'];

	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function push_notifications()
	{
		return $this->hasMany(PushNotification::class);
	}

    public function user_type(){

        return $this->morphTo();
    }

}
