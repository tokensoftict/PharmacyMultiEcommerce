<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankAccount
 * 
 * @property int $id
 * @property int|null $bank_id
 * @property string|null $account_number
 * @property string|null $account_name
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Bank|null $bank
 *
 * @package App\Models
 */
class BankAccount extends Model
{
	protected $table = 'bank_accounts';

	protected $casts = [
		'bank_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'bank_id',
		'account_number',
		'account_name',
		'status'
	];

	public function bank()
	{
		return $this->belongsTo(Bank::class);
	}
}
