<?php

namespace App\Models;

use App\Services\Opay\OpayRsaCrypto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Stores OPay Offline API (POS & Others) authentication credentials and
 * dashboard configuration fields.
 *
 * Sensitive fields (`client_auth_key`, `merchant_private_key`) are encrypted at
 * rest via Laravel's built-in `encrypted` Eloquent cast.
 */
class OpaySetting extends Model
{
    protected $table = 'opay_settings';

    protected $fillable = [
        'is_enabled',
        'environment',
        'base_url',
        'api_version',
        'head_merchant_id',
        'merchant_id',
        'client_auth_key',
        'opay_public_key',
        'merchant_public_key',
        'merchant_private_key',
        'ip_whitelisting',
        'webhook_url',
        'key_bits',
        'keys_generated_at',
        'last_tested_at',
        'last_test_status',
        'last_test_message',
        'updated_by',
    ];

    protected $hidden = [
        'merchant_private_key',
        'client_auth_key',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'client_auth_key' => 'encrypted',
            'merchant_private_key' => 'encrypted',
            'key_bits' => 'integer',
            'keys_generated_at' => 'datetime',
            'last_tested_at' => 'datetime',
        ];
    }

    /**
     * Fetch the singleton OPay settings record, or return an unsaved instance
     * populated with `config('opay')` defaults.
     */
    public static function current(): self
    {
        $record = Schema::hasTable('opay_settings')
            ? static::query()->orderBy('id')->first()
            : null;

        if ($record) {
            return $record;
        }

        return new self([
            'is_enabled' => (bool) config('opay.enabled', false),
            'environment' => (string) config('opay.environment', 'live'),
            'base_url' => (string) config('opay.base_url', 'https://payapi.opayweb.com'),
            'api_version' => (string) config('opay.api_version', 'V1.0.1'),
            'head_merchant_id' => config('opay.head_merchant_id'),
            'merchant_id' => config('opay.merchant_id'),
            'client_auth_key' => config('opay.client_auth_key'),
            'opay_public_key' => config('opay.opay_public_key'),
            'merchant_public_key' => config('opay.merchant_public_key'),
            'merchant_private_key' => config('opay.merchant_private_key'),
            'ip_whitelisting' => config('opay.ip_whitelisting'),
            'webhook_url' => config('opay.webhook_url') ?: self::defaultWebhookUrl(),
            'key_bits' => (int) config('opay.key_bits', 1024),
        ]);
    }

    public static function defaultWebhookUrl(): string
    {
        try {
            return route('api.opay.webhook');
        } catch (\Throwable) {
            return rtrim((string) config('app.url', 'https://generaldrugcentre.com'), '/') . '/api/opay/webhook';
        }
    }

    public function hasKeyPair(): bool
    {
        return filled($this->merchant_public_key) && filled($this->merchant_private_key);
    }

    public function isConfigured(): bool
    {
        return $this->hasKeyPair()
            && filled($this->opay_public_key)
            && filled($this->client_auth_key)
            && filled($this->base_url);
    }

    public function getRawMerchantPublicKey(): ?string
    {
        if (! filled($this->merchant_public_key)) {
            return null;
        }

        return OpayRsaCrypto::stripPemHeaders($this->merchant_public_key);
    }

    public function getRawOpayPublicKey(): ?string
    {
        if (! filled($this->opay_public_key)) {
            return null;
        }

        return OpayRsaCrypto::stripPemHeaders($this->opay_public_key);
    }

    /**
     * @return array<int, string>
     */
    public function getIpWhitelistArray(): array
    {
        if (! filled($this->ip_whitelisting)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', (string) $this->ip_whitelisting)),
            static fn (string $ip): bool => $ip !== ''
        ));
    }

    public function getEffectiveWebhookUrl(): string
    {
        return filled($this->webhook_url) ? (string) $this->webhook_url : self::defaultWebhookUrl();
    }
}
