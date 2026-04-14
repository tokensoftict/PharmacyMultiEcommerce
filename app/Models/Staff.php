<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $fillable = [
        'local_id',
        'name',
        'email',
        'phone',
        'username',
        'department',
        'status',
    ];

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
