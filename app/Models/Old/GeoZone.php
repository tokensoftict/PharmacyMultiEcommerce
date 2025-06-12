<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property ZoneToGeoZone[] $zoneToGeoZones
 */
class GeoZone extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'geo_zone';
    protected $keyType = 'integer';

    protected $fillable = ['name', 'description', 'created_at', 'updated_at'];

    public function zoneToGeoZones()
    {
        return $this->hasMany(ZoneToGeoZone::class);
    }
}
