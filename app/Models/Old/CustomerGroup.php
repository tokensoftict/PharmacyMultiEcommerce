<?php

namespace App\Models\Old;


use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property int $status
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property User[] $users
 */
class CustomerGroup extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'customer_group';

    protected $keyType = 'integer';

    public static $validation = [
        "name"=>'required',
    ];

    protected $fillable = ['name', 'status', 'description', 'created_at', 'updated_at'];

    public static $fields = ['name', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
