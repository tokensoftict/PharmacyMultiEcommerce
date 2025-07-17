<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'full_name',
        'phone_number',
        'department',
        'invoice_number',
        'staff_name',
        'feedback_type',
        'feedback',
    ];
}
