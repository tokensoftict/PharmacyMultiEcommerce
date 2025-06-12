<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property Address[] $addresses
 * @property LocalGovt[] $localGovts
 * @property ZoneToGeoZone[] $zoneToGeoZones
 */
class Zone extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'zone';
    protected $keyType = 'integer';

    protected $fillable = ['name', 'status', 'created_at', 'updated_at'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function localGovts()
    {
        return $this->hasMany(LocalGovt::class, 'state_id');
    }

    public function zoneToGeoZones()
    {
        return $this->hasMany(ZoneToGeoZone::class);
    }
}
