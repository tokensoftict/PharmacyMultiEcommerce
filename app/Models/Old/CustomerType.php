<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property User[] $users
 */
class CustomerType extends Model
{
    protected $table = 'customer_type';
    protected $keyType = 'integer';

    protected $fillable = ['name', 'status', 'created_at', 'updated_at'];

    public function users()
    {
        return $this->hasMany(User::class, 'type');
    }

}
