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
        'kafka_topic',
        'kafka_status',
        'kafka_forwarded_at',
        'kafka_error',
    ];

    protected function casts(): array
    {
        return [
            'signature_verified' => 'boolean',
            'headers' => 'array',
            'payload' => 'array',
            'processed_at' => 'datetime',
            'kafka_forwarded_at' => 'datetime',
        ];
    }
}
