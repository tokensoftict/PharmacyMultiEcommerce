<?php

namespace App\Events\Opay;

use App\Models\OpayWebhookLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpayWebhookReceived
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $headers
     */
    public function __construct(
        public readonly array $data,
        public readonly array $headers,
        public readonly ?OpayWebhookLog $log = null,
    ) {
    }
}
