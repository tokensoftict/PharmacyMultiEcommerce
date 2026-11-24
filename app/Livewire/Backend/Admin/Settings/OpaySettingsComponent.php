<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\PermissionAttribute;
use App\Models\OpaySetting;
use App\Models\OpayWebhookLog;
use App\Services\Opay\OpayClient;
use App\Services\Opay\OpayRsaCrypto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin UI for configuring OPay Offline Payments API authentication & webhooks.
 *
 * Mirrors the OPay Business Dashboard ("POS & Others") credentials flow:
 *  - Generate / manage Merchant RSA Key Pair (1024-bit default per OPay docs, or 2048-bit)
 *  - Copy Merchant Public Key, IP WhiteListing, and Webhook URL to OPay Dashboard
 *  - Save OPay Public Key and Client Auth Key provided by OPay
 *  - Run local RSA cryptographic self-test and live OPay API connection test
 */
class OpaySettingsComponent extends Component
{
    public bool $is_enabled = false;
    public string $environment = 'live';
    public string $base_url = 'https://payapi.opayweb.com';
    public string $api_version = 'V1.0.1';
    public string $head_merchant_id = '';
    public string $merchant_id = '';

    // OPay-provided credentials
    public string $opay_public_key = '';
    public string $client_auth_key = '';
    public bool $has_existing_client_auth_key = false;
    public string $client_auth_key_masked = '';

    // Merchant RSA key pair
    public string $merchant_public_key = '';
    public string $merchant_public_key_raw = '';
    public bool $has_merchant_private_key = false;
    public int $key_bits = 1024;
    public ?string $keys_generated_at = null;

    // Optional manual key import (advanced)
    public bool $showImportKeys = false;
    public string $import_merchant_public_key = '';
    public string $import_merchant_private_key = '';

    // Dashboard fields
    public string $ip_whitelisting = '';
    public string $webhook_url = '';
    public string $detected_server_ip = '';

    // Status & diagnostics
    public ?string $last_tested_at = null;
    public ?string $last_test_status = null;
    public ?string $last_test_message = null;
    public ?array $diagnosticResult = null;

    private OpayRsaCrypto $crypto;

    public function boot(OpayRsaCrypto $crypto): void
    {
        $this->crypto = $crypto;
    }

    public function mount(): void
    {
        $this->loadFromSettings(OpaySetting::current());
        $this->detected_server_ip = $this->detectServerIp();
    }

    public function render()
    {
        $recentWebhooks = Schema::hasTable('opay_webhook_logs')
            ? OpayWebhookLog::query()->latest()->limit(10)->get()
            : collect();

        $settings = OpaySetting::current();

        return view('livewire.pages.backend.admin.settings.opay-settings-component', [
            'isConfigured' => $settings->isConfigured(),
            'recentWebhooks' => $recentWebhooks,
            'defaultWebhookUrl' => OpaySetting::defaultWebhookUrl(),
        ]);
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.opay_settings')]
    public function view(): void
    {
    }

    /**
     * Save all OPay dashboard and API settings.
     */
    #[PermissionAttribute('Save', 'save', 'backend.admin.settings.opay_settings.update')]
    public function save(): void
    {
        $this->authorizeUpdate();

        $this->ip_whitelisting = $this->normalizeIpList($this->ip_whitelisting);
        $this->base_url = rtrim(trim($this->base_url), '/');
        $this->webhook_url = trim($this->webhook_url);
        $this->opay_public_key = trim($this->opay_public_key);
        $this->client_auth_key = trim($this->client_auth_key);

        $this->validate([
            'is_enabled' => ['boolean'],
            'environment' => ['required', Rule::in(['live', 'sandbox'])],
            'base_url' => ['required', 'url', 'max:255'],
            'api_version' => ['required', 'string', 'max:32'],
            'head_merchant_id' => ['nullable', 'string', 'max:64'],
            'merchant_id' => ['nullable', 'string', 'max:64'],
            'key_bits' => ['required', Rule::in([1024, 2048])],
            'client_auth_key' => ['nullable', 'string', 'max:128'],
            'opay_public_key' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filled($value) && ! $this->crypto->isValidPublicKey((string) $value)) {
                        $fail('The OPay Public Key is not a valid RSA public key (paste either raw Base64 or PEM format).');
                    }
                },
            ],
            'ip_whitelisting' => [
                'nullable',
                'string',
                'max:1000',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! filled($value)) {
                        return;
                    }
                    foreach (explode(',', (string) $value) as $ip) {
                        $ip = trim($ip);
                        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
                            $fail("Invalid IP address in IP WhiteListing: '{$ip}'. Use comma-separated IPv4/IPv6 addresses.");

                            return;
                        }
                    }
                },
            ],
            'webhook_url' => ['nullable', 'url', 'max:500'],
        ]);

        $setting = OpaySetting::query()->orderBy('id')->first() ?? new OpaySetting();

        $setting->is_enabled = $this->is_enabled;
        $setting->environment = $this->environment;
        $setting->base_url = $this->base_url ?: 'https://payapi.opayweb.com';
        $setting->api_version = $this->api_version ?: 'V1.0.1';
        $setting->head_merchant_id = filled($this->head_merchant_id) ? trim($this->head_merchant_id) : null;
        $setting->merchant_id = filled($this->merchant_id) ? trim($this->merchant_id) : null;
        $setting->key_bits = $this->key_bits;
        $setting->ip_whitelisting = filled($this->ip_whitelisting) ? $this->ip_whitelisting : null;
        $setting->webhook_url = filled($this->webhook_url) ? $this->webhook_url : OpaySetting::defaultWebhookUrl();

        if (filled($this->opay_public_key)) {
            $setting->opay_public_key = OpayRsaCrypto::normalizePublicKeyToPem($this->opay_public_key);
        } elseif ($this->opay_public_key === '') {
            $setting->opay_public_key = null;
        }

        // Only overwrite client_auth_key when explicitly entered so edits to other fields don't clear it.
        if (filled($this->client_auth_key)) {
            $setting->client_auth_key = $this->client_auth_key;
        }

        if (auth()->check()) {
            $setting->updated_by = auth()->id();
        }

        $setting->save();

        $this->loadFromSettings($setting->fresh());
        $this->alert('success', 'OPay authentication settings saved successfully.');
    }

    /**
     * Generate a brand new RSA key pair and persist it immediately so the user
     * can copy the Merchant Public Key into the OPay Business Dashboard.
     */
    public function generateKeyPair(): void
    {
        $this->authorizeUpdate();

        $this->validate([
            'key_bits' => ['required', Rule::in([1024, 2048])],
        ]);

        $pair = $this->crypto->generateKeyPair($this->key_bits);

        $setting = OpaySetting::query()->orderBy('id')->first() ?? new OpaySetting([
            'environment' => $this->environment,
            'base_url' => $this->base_url ?: 'https://payapi.opayweb.com',
            'api_version' => $this->api_version ?: 'V1.0.1',
            'webhook_url' => filled($this->webhook_url) ? $this->webhook_url : OpaySetting::defaultWebhookUrl(),
        ]);

        $setting->merchant_public_key = $pair['public_key_pem'];
        $setting->merchant_private_key = $pair['private_key_pem'];
        $setting->key_bits = $pair['bits'];
        $setting->keys_generated_at = now();
        if (auth()->check()) {
            $setting->updated_by = auth()->id();
        }
        $setting->save();

        $this->loadFromSettings($setting->fresh());
        $this->showImportKeys = false;
        $this->diagnosticResult = null;

        $this->alert('success', "Generated {$pair['bits']}-bit RSA key pair! Copy the Merchant Public Key below into your OPay Dashboard.");
    }

    /**
     * Import an externally generated Merchant RSA key pair (e.g. via OpenSSL CLI).
     */
    public function importKeyPair(): void
    {
        $this->authorizeUpdate();

        $this->validate([
            'import_merchant_public_key' => ['required', 'string'],
            'import_merchant_private_key' => ['required', 'string'],
        ]);

        $pub = trim($this->import_merchant_public_key);
        $priv = trim($this->import_merchant_private_key);

        if (! $this->crypto->isValidPublicKey($pub)) {
            $this->addError('import_merchant_public_key', 'Invalid RSA public key.');

            return;
        }

        if (! $this->crypto->isValidPrivateKey($priv)) {
            $this->addError('import_merchant_private_key', 'Invalid RSA private key.');

            return;
        }

        if (! $this->crypto->validateKeyPair($pub, $priv)) {
            $this->addError('import_merchant_private_key', 'The Merchant Private Key does not match the Merchant Public Key.');

            return;
        }

        $setting = OpaySetting::query()->orderBy('id')->first() ?? new OpaySetting([
            'environment' => $this->environment,
            'base_url' => $this->base_url ?: 'https://payapi.opayweb.com',
            'api_version' => $this->api_version ?: 'V1.0.1',
            'webhook_url' => filled($this->webhook_url) ? $this->webhook_url : OpaySetting::defaultWebhookUrl(),
        ]);

        $setting->merchant_public_key = OpayRsaCrypto::normalizePublicKeyToPem($pub);
        $setting->merchant_private_key = OpayRsaCrypto::normalizePrivateKeyToPem($priv);
        $setting->keys_generated_at = now();
        if (auth()->check()) {
            $setting->updated_by = auth()->id();
        }
        $setting->save();

        $this->import_merchant_public_key = '';
        $this->import_merchant_private_key = '';
        $this->showImportKeys = false;
        $this->loadFromSettings($setting->fresh());

        $this->alert('success', 'Custom Merchant RSA key pair verified and saved securely.');
    }

    /**
     * Clear the stored Client Auth Key so a new one can be entered cleanly.
     */
    public function clearClientAuthKey(): void
    {
        $this->authorizeUpdate();

        $setting = OpaySetting::query()->orderBy('id')->first();
        if ($setting) {
            $setting->client_auth_key = null;
            $setting->save();
            $this->loadFromSettings($setting->fresh());
        } else {
            $this->client_auth_key = '';
            $this->has_existing_client_auth_key = false;
            $this->client_auth_key_masked = '';
        }

        $this->alert('success', 'Client Auth Key cleared.');
    }

    /**
     * Download the current Merchant Public Key in PEM format (`client_public_key_php_dotnet.pem`).
     */
    public function downloadPublicKey(): ?StreamedResponse
    {
        $setting = OpaySetting::current();
        if (! filled($setting->merchant_public_key)) {
            $this->alert('error', 'No Merchant Public Key has been generated yet.');

            return null;
        }

        $pem = OpayRsaCrypto::normalizePublicKeyToPem((string) $setting->merchant_public_key) . "\n";

        return response()->streamDownload(static function () use ($pem): void {
            echo $pem;
        }, 'client_public_key_php_dotnet.pem', [
            'Content-Type' => 'application/x-pem-file',
        ]);
    }

    /**
     * Run an offline cryptographic round-trip check on the saved RSA keys.
     */
    public function verifyCrypto(): void
    {
        $setting = OpaySetting::current();

        if (! $setting->hasKeyPair()) {
            $this->diagnosticResult = [
                'ok' => false,
                'title' => 'Merchant RSA Key Pair Missing',
                'message' => 'Generate or import a Merchant RSA key pair first.',
            ];

            return;
        }

        if (! $this->crypto->validateKeyPair((string) $setting->merchant_public_key, (string) $setting->merchant_private_key)) {
            $this->diagnosticResult = [
                'ok' => false,
                'title' => 'Key Pair Mismatch',
                'message' => 'The stored Merchant Public Key and Private Key do not match.',
            ];

            return;
        }

        try {
            $probePayload = [
                'opayMerchantId' => $setting->merchant_id ?: '256621072768092',
                'remark' => str_repeat('mystore-opay-chunk-test-', 8), // > 117 bytes to exercise multi-block RSA chunking
            ];
            $timestamp = OpayRsaCrypto::currentTimestampMs();

            // 1. Encrypt & decrypt round-trip with Merchant key pair
            $encryptedSelf = $this->crypto->encryptByPublicKey(
                $this->crypto->canonicalJson($probePayload),
                (string) $setting->merchant_public_key,
            );
            $decryptedSelf = $this->crypto->decryptByPrivateKey($encryptedSelf, (string) $setting->merchant_private_key);
            if ($decryptedSelf !== $this->crypto->canonicalJson($probePayload)) {
                throw new \RuntimeException('Chunked RSA encrypt/decrypt round-trip mismatch.');
            }

            // 2. If OPay Public Key is configured, verify request envelope encryption & signing too
            $opayCheck = 'OPay Public Key not yet entered (enter it after saving your Merchant Public Key on OPay Dashboard).';
            if (filled($setting->opay_public_key)) {
                $envelope = $this->crypto->buildEncryptedRequest(
                    payload: $probePayload,
                    timestamp: $timestamp,
                    opayPublicKey: (string) $setting->opay_public_key,
                    merchantPrivateKey: (string) $setting->merchant_private_key,
                );
                $sigValid = $this->crypto->verify(
                    $envelope['paramContent'] . $timestamp,
                    $envelope['sign'],
                    (string) $setting->merchant_public_key,
                );
                if (! $sigValid) {
                    throw new \RuntimeException('Generated request signature failed verification.');
                }
                $opayCheck = 'OPay Public Key encryption & SHA256withRSA request signing verified.';
            }

            $this->diagnosticResult = [
                'ok' => true,
                'title' => 'Cryptographic Self-Test Passed',
                'message' => "Merchant RSA key pair & multi-chunk PKCS#1 encryption verified. {$opayCheck}",
            ];
        } catch (\Throwable $e) {
            $this->diagnosticResult = [
                'ok' => false,
                'title' => 'Cryptographic Self-Test Failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a live authenticated request (`queryMerchantBalance`) to OPay API.
     */
    public function testConnection(): void
    {
        $client = app(OpayClient::class);
        $result = $client->testConnection($this->merchant_id ?: $this->head_merchant_id);

        $this->loadFromSettings(OpaySetting::current());
        $this->diagnosticResult = [
            'ok' => $result['ok'],
            'title' => $result['ok'] ? 'OPay Live Connection Successful' : 'OPay Connection Failed',
            'message' => $result['message'],
        ];

        $this->alert($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function resetWebhookUrl(): void
    {
        $this->webhook_url = OpaySetting::defaultWebhookUrl();
    }

    public function appendDetectedServerIp(): void
    {
        if (! filled($this->detected_server_ip) || filter_var($this->detected_server_ip, FILTER_VALIDATE_IP) === false) {
            return;
        }

        $ips = array_filter(array_map('trim', explode(',', $this->ip_whitelisting)));
        if (! in_array($this->detected_server_ip, $ips, true)) {
            $ips[] = $this->detected_server_ip;
        }
        $this->ip_whitelisting = implode(',', $ips);
    }

    private function loadFromSettings(OpaySetting $setting): void
    {
        $this->is_enabled = (bool) $setting->is_enabled;
        $this->environment = (string) ($setting->environment ?: 'live');
        $this->base_url = (string) ($setting->base_url ?: 'https://payapi.opayweb.com');
        $this->api_version = (string) ($setting->api_version ?: 'V1.0.1');
        $this->head_merchant_id = (string) ($setting->head_merchant_id ?? '');
        $this->merchant_id = (string) ($setting->merchant_id ?? '');
        $this->opay_public_key = (string) ($setting->getRawOpayPublicKey() ?? '');

        $existingAuthKey = (string) ($setting->client_auth_key ?? '');
        $this->has_existing_client_auth_key = $existingAuthKey !== '';
        $this->client_auth_key_masked = $existingAuthKey !== ''
            ? $this->maskSecret($existingAuthKey)
            : '';
        $this->client_auth_key = '';

        $this->merchant_public_key = (string) ($setting->merchant_public_key ?? '');
        $this->merchant_public_key_raw = (string) ($setting->getRawMerchantPublicKey() ?? '');
        $this->has_merchant_private_key = filled($setting->merchant_private_key);
        $this->key_bits = (int) ($setting->key_bits ?: 1024);
        $this->keys_generated_at = $setting->keys_generated_at?->format('Y-m-d H:i:s');

        $this->ip_whitelisting = (string) ($setting->ip_whitelisting ?? '');
        $this->webhook_url = $setting->getEffectiveWebhookUrl();

        $this->last_tested_at = $setting->last_tested_at?->format('Y-m-d H:i:s');
        $this->last_test_status = $setting->last_test_status;
        $this->last_test_message = $setting->last_test_message;
    }

    private function normalizeIpList(string $raw): string
    {
        $parts = array_filter(
            array_map('trim', explode(',', $raw)),
            static fn (string $v): bool => $v !== ''
        );

        return implode(',', array_values(array_unique($parts)));
    }

    private function maskSecret(string $secret): string
    {
        $len = strlen($secret);
        if ($len <= 8) {
            return str_repeat('•', $len);
        }

        return substr($secret, 0, 4) . str_repeat('•', max(4, $len - 8)) . substr($secret, -4);
    }

    private function detectServerIp(): string
    {
        $serverAddr = request()->server('SERVER_ADDR');
        if (is_string($serverAddr) && filter_var($serverAddr, FILTER_VALIDATE_IP) !== false) {
            return $serverAddr;
        }

        $host = gethostname();
        if (is_string($host) && $host !== '') {
            $ip = gethostbyname($host);
            if ($ip !== $host && filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                return $ip;
            }
        }

        return '';
    }

    private function authorizeUpdate(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $canUpdate = $user->can('backend.admin.settings.opay_settings.update')
            || $user->can('backend.admin.settings.system_settings.update');

        abort_unless($canUpdate, 403, 'You do not have permission to update OPay settings.');
    }
}
