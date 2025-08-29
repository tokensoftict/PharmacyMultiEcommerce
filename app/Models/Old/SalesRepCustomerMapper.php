<?php

namespace App\Models\Old;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $customer_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $customer
 * @property User $user
 */
class SalesRepCustomerMapper extends Model
{
    protected $connection = 'old_server_mysql';
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sales_rep_customer_mapper';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'customer_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\Old\User::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Old\User::class,'user_id');
    }
}
