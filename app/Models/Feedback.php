<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'full_name',
        'phone_number',
        'store',
        'department',
        'invoice_number',
        'rating',
        'staff_id',
        'feedback_type',
        'feedback',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
