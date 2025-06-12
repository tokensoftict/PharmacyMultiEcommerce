<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Classes\ApplicationEnvironment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Address
 *
 * @property int $id
 * @property string|null $name
 * @property int $user_id
 * @property int $local_address_id
 * @property string $address_1
 * @property string|null $address_2
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $town_id
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Country|null $country
 * @property State|null $state
 * @property Town|null $town
 * @property User $user
 * @property Collection|Order[] $orders
 *
 * @package App\Models
 */
class Address extends Model
{
	protected $table = 'addresses';

    protected $with =['country', 'state', 'town'];

	protected $casts = [
		'user_id' => 'int',
		'country_id' => 'int',
		'state_id' => 'int',
		'town_id' => 'int',
        'local_address_id' => 'int',
		'deleted' => 'bool'
	];

	protected $fillable = [
		'name',
		'user_id',
		'address_1',
		'address_2',
		'country_id',
		'state_id',
		'town_id',
        'local_address_id',
		'deleted'
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function town()
	{
		return $this->belongsTo(Town::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class, 'shipping_address_id');
	}


    public function getFullAddressAttribute()
    {
        return $this->address_1.", ".$this->address_2.", ".$this->town->name.", ".$this->state->name.", ".$this->country->name;
    }

    public function isDefault()
    {
        return $this->id  === ApplicationEnvironment::getApplicationRelatedModel()?->address_id;
    }
}
