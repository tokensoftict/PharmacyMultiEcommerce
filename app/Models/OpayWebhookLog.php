<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpayWebhookLog extends Model
{
    protected $table = 'opay_webhook_logs';

    protected $fillable = [
        'transaction_id',
        'merchant_id',
        'out_order_no',
        'order_no',
        'pay_no',
        'status',
        'amount',
        'currency',
        'signature_verified',
        'source_ip',
        'headers',
        'payload',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'signature_verified' => 'boolean',
            'headers' => 'array',
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
