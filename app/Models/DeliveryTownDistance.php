<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryTownDistance
 *
 * @property int $id
 * @property float $town_distance
 * @property int $town_id
 * @property string $type
 * @property string $delivery_type
 * @property string $frequency
 * @property string $interval_frequency
 * @property int $no
 * @property int $interval_no
 * @property float $minimum_shipping_amount
 * @property float $fixed_shipping_amount
 * @property string|null $delivery_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $starting_date
 *
 * @property Town $town
 *
 * @package App\Models
 */
class DeliveryTownDistance extends Model
{
	protected $table = 'delivery_town_distances';

	protected $casts = [
		'town_distance' => 'float',
		'town_id' => 'int',
		'no' => 'int',
        'interval_no' => 'int',
		'minimum_shipping_amount' => 'float',
		'fixed_shipping_amount' => 'float',
        'delivery_days' => 'array',
        'starting_date' => 'date'
	];

	protected $fillable = [
		'town_distance',
		'town_id',
		'type',
		'frequency',
		'no',
		'minimum_shipping_amount',
		'fixed_shipping_amount',
		'delivery_days',

        'delivery',
        'interval_no',
        'interval_frequency',
        'starting_date'
	];

	public function town()
	{
		return $this->belongsTo(Town::class);
	}
}
