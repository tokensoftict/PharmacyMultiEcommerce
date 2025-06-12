<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $zone_id
 * @property integer $local_govt_id
 * @property string $town_name
 * @property string $created_at
 * @property string $updated_at
 * @property LocalGovt $localGovt
 * @property Zone $zone
 * @property Address[] $addresses
 */
class Town extends Model
{
    protected $connection = 'old_server_mysql';

    protected $fillable = ['zone_id', 'local_govt_id', 'town_name', 'created_at', 'updated_at'];
    public function localGovt()
    {
        return $this->belongsTo(LocalGovt::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}
