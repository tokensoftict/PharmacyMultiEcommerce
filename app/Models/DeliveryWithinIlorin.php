<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryWithinIlorin extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'name',
        'amount',
        'status',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
