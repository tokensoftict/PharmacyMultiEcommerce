<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $zone_id
 * @property integer $geo_zone_id
 * @property string $created_at
 * @property string $updated_at
 * @property GeoZone $geoZone
 * @property Zone $zone
 */
class ZoneToGeoZone extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'zone_to_geo_zone';
    protected $keyType = 'integer';

    protected $fillable = ['zone_id', 'geo_zone_id', 'created_at', 'updated_at'];

    public function geoZone()
    {
        return $this->belongsTo(GeoZone::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
