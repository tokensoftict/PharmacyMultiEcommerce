<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Validator;


class Address extends Model
{
    protected $connection = 'old_server_mysql';

    protected $table = 'address';
    protected $keyType = 'integer';

    protected $fillable = ['town_id','user_id','user_type','town_id' ,'zone_id', 'firstname', 'lastname', 'company', 'address_1', 'address_2', 'city', 'postcode', 'country_id', 'created_at', 'updated_at'];

    public $fields = ['town_id','zone_id', 'firstname', 'lastname','town_id' ,'company', 'address_1', 'address_2', 'city', 'postcode'];

    public static $validation = [
        'firstname'   =>'required',
        'lastname'    =>'required',
        'zone_id'     =>'required',
        'address_1'   =>'required'
    ];

    public function town()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

}
