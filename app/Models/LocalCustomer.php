<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LocalCustomer
 * 
 * @property int $id
 * @property int|null $local_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $email
 * @property string|null $address
 * @property string|null $phone_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class LocalCustomer extends Model
{
	protected $table = 'local_customers';

	protected $casts = [
		'local_id' => 'int'
	];

	protected $fillable = [
		'local_id',
		'firstname',
		'lastname',
		'email',
		'address',
		'phone_number'
	];
}
