<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $state_id
 * @property string $local_name
 * @property Zone $zone
 */
class LocalGovt extends Model
{

    protected $connection = 'old_server_mysql';

    protected $fillable = ['state_id', 'local_name'];
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
