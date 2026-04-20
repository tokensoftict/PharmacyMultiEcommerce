<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalInvoice extends Model
{
    protected $table = 'local_invoices';

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'user_id',
        'app_id',
        'department',
        'invoice_date',
        'customer_phone_number',
        'total',
        'payment_methods',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function customer()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
