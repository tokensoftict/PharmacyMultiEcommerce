<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageComponent extends Model
{
    protected $fillable = [
        'app_id',
        'component_name',
        'type',
        'component_id',
        'label',
        'limit',
        'see_all_link',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'limit' => 'integer',
        'sort_order' => 'integer',
        'app_id' => 'integer',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
