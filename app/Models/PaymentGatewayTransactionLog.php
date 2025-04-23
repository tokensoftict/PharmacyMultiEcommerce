<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentGatewayTransactionLog
 * 
 * @property int $id
 * @property string $transaction_reference
 * @property string $gateway
 * @property string $status
 * @property float $total
 * @property string $email
 * @property string $phone
 * @property string $currency
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class PaymentGatewayTransactionLog extends Model
{
	protected $table = 'payment_gateway_transaction_logs';

	protected $casts = [
		'total' => 'float',
		'user_id' => 'int'
	];

	protected $fillable = [
		'transaction_reference',
		'gateway',
		'status',
		'total',
		'email',
		'phone',
		'currency',
		'user_id'
	];
}
