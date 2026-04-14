<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'department',
        'status',
    ];

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
