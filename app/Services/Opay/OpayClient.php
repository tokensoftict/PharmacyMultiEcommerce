<?php

namespace App\Services\Opay;

use App\Models\OpaySetting;
use App\Services\Opay\Exceptions\OpayApiException;
use App\Services\Opay\Exceptions\OpayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Authenticated HTTP client for OPay Offline Payments API (`https://payapi.opayweb.com`).
 *
 * Automatically handles:
 *  - Common OPay headers (`clientAuthKey`, `version`, `bodyFormat`, `timestamp`)
 *  - Request RSA encryption (`paramContent`) and SHA256withRSA signing (`sign`)
 *  - Response SHA256withRSA signature verification and RSA decryption (`data`)
 */
class OpayClient
{
    public function __construct(
        private readonly OpayRsaCrypto $crypto,
        private ?OpaySetting $settings = null,
    ) {
    }

    public function getSettings(bool $fresh = false): OpaySetting
    {
        if ($fresh || $this->settings === null) {
            $this->settings = OpaySetting::current();
        }

        return $this->settings;
    }

    public function usingSettings(OpaySetting $settings): self
    {
        return new self($this->crypto, $settings);
    }

    public function isReady(): bool
    {
        $settings = $this->getSettings();

        return $settings->is_enabled && $settings->isConfigured();
    }

    /**
     * Send an encrypted & signed POST request to an OPay Offline API endpoint
     * and return the verified, decrypted response payload.
     *
     * @param  string  $endpoint  Relative path (e.g. `/openApi/merchant/queryMerchantBalance`) or full URL
     * @param  array<string, mixed>  $payload  Plaintext JSON parameters before encryption
     * @param  string|null  $timestamp  Optional UTC epoch milliseconds (defaults to current time)
     * @param  bool  $ignoreEnabledFlag  Set true when explicitly testing credentials from the Settings UI
     * @return array{code: string, message: string, timestamp: string|null, verified: bool, data: array|null}
     */
    public function post(
        string $endpoint,
        array $payload = [],
        ?string $timestamp = null,
        bool $ignoreEnabledFlag = false,
    ): array {
        $settings = $this->getSettings();

        if (! $ignoreEnabledFlag && ! $settings->is_enabled) {
            throw new OpayException('OPay integration is currently disabled in Settings.');
        }

        $this->assertConfigured($settings);

        $timestamp ??= OpayRsaCrypto::currentTimestampMs();
        $url = $this->resolveUrl($settings, $endpoint);

        $requestBody = $this->crypto->buildEncryptedRequest(
            payload: $payload,
            timestamp: $timestamp,
            opayPublicKey: (string) $settings->opay_public_key,
            merchantPrivateKey: (string) $settings->merchant_private_key,
        );

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'clientAuthKey' => (string) $settings->client_auth_key,
            'version' => (string) ($settings->api_version ?: config('opay.api_version', 'V1.0.1')),
            'bodyFormat' => (string) config('opay.body_format', 'JSON'),
            'timestamp' => $timestamp,
        ];

        try {
            $httpResponse = Http::withHeaders($headers)
                ->timeout((int) config('opay.timeout', 30))
                ->connectTimeout((int) config('opay.connect_timeout', 10))
                ->post($url, $requestBody);
        } catch (ConnectionException $e) {
            throw new OpayApiException(
                'Unable to connect to OPay API: ' . $e->getMessage(),
                previous: $e,
            );
        }

        $json = $httpResponse->json();
        if (! is_array($json)) {
            throw new OpayApiException(
                "Unexpected non-JSON response from OPay (HTTP {$httpResponse->status()}).",
                code: $httpResponse->status(),
            );
        }

        if (! $httpResponse->successful() && ($json['code'] ?? null) !== '00000') {
            $opayCode = isset($json['code']) ? (string) $json['code'] : (string) $httpResponse->status();
            $opayMessage = isset($json['message']) ? (string) $json['message'] : $httpResponse->body();

            throw new OpayApiException(
                "OPay HTTP {$httpResponse->status()} [{$opayCode}]: {$opayMessage}",
                opayCode: $opayCode,
                opayMessage: $opayMessage,
                rawResponse: $json,
                code: $httpResponse->status(),
            );
        }

        $verified = $this->crypto->verifyAndDecryptResponse(
            response: $json,
            opayPublicKey: (string) $settings->opay_public_key,
            merchantPrivateKey: (string) $settings->merchant_private_key,
            requireSuccessCode: true,
        );

        return [
            'code' => (string) ($json['code'] ?? '00000'),
            'message' => (string) ($json['message'] ?? 'SUCCESSFUL'),
            'timestamp' => isset($json['timestamp']) ? (string) $json['timestamp'] : null,
            'verified' => $verified['verified'],
            'data' => $verified['data'],
        ];
    }

    /**
     * Query OPay Merchant Account Balance (`POST /openApi/merchant/queryMerchantBalance`).
     */
    public function queryMerchantBalance(?string $opayMerchantId = null, bool $ignoreEnabledFlag = false): array
    {
        $settings = $this->getSettings();
        $merchantId = $opayMerchantId ?: ($settings->merchant_id ?: $settings->head_merchant_id);

        if (! filled($merchantId)) {
            throw new OpayException('Merchant ID (or Head Merchant ID) is required to query OPay account balance.');
        }

        return $this->post(
            endpoint: '/openApi/merchant/queryMerchantBalance',
            payload: ['opayMerchantId' => (string) $merchantId],
            ignoreEnabledFlag: $ignoreEnabledFlag,
        );
    }

    /**
     * Query OPay Order Details (`POST /openApi/order/queryOrderDetail`).
     */
    public function queryOrderDetail(array $params): array
    {
        return $this->post('/openApi/order/queryOrderDetail', $params);
    }

    /**
     * Create an OPay Checkout / POS Order (`POST /openApi/order/checkout/createOrder`).
     */
    public function createCheckoutOrder(array $params): array
    {
        $settings = $this->getSettings();

        if (! isset($params['headMerchantId']) && filled($settings->head_merchant_id)) {
            $params['headMerchantId'] = (string) $settings->head_merchant_id;
        }

        if (! isset($params['merchantId']) && filled($settings->merchant_id)) {
            $params['merchantId'] = (string) $settings->merchant_id;
        }

        return $this->post('/openApi/order/checkout/createOrder', $params);
    }

    /**
     * Test live connectivity and RSA authentication against OPay's Query Balance endpoint.
     *
     * @return array{ok: bool, message: string, data: array|null}
     */
    public function testConnection(?string $opayMerchantId = null): array
    {
        $settings = $this->getSettings(fresh: true);

        try {
            $result = $this->queryMerchantBalance($opayMerchantId, ignoreEnabledFlag: true);
            $data = $result['data'] ?? [];
            $accountName = $data['opayMerchantName'] ?? 'Merchant';
            $usableKobo = isset($data['usableAmount']) ? (float) $data['usableAmount'] : null;
            $currency = $data['currency'] ?? 'NGN';
            $balanceDisplay = $usableKobo !== null
                ? sprintf('%s %s', $currency, number_format($usableKobo / 100, 2))
                : 'Verified';

            $message = "Authenticated successfully with OPay ({$accountName} — Balance: {$balanceDisplay}).";

            if ($settings->exists) {
                $settings->forceFill([
                    'last_tested_at' => now(),
                    'last_test_status' => 'success',
                    'last_test_message' => $message,
                ])->save();
            }

            return [
                'ok' => true,
                'message' => $message,
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            if ($settings->exists) {
                $settings->forceFill([
                    'last_tested_at' => now(),
                    'last_test_status' => 'failed',
                    'last_test_message' => $e->getMessage(),
                ])->save();
            }

            return [
                'ok' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    private function assertConfigured(OpaySetting $settings): void
    {
        $missing = [];

        if (! filled($settings->client_auth_key)) {
            $missing[] = 'Client Auth Key';
        }
        if (! filled($settings->opay_public_key)) {
            $missing[] = 'OPay Public Key';
        }
        if (! filled($settings->merchant_private_key)) {
            $missing[] = 'Merchant Private Key';
        }
        if (! filled($settings->base_url)) {
            $missing[] = 'OPay Base URL';
        }

        if ($missing !== []) {
            throw new OpayException('OPay authentication is incomplete. Missing: ' . implode(', ', $missing) . '.');
        }
    }

    private function resolveUrl(OpaySetting $settings, string $endpoint): string
    {
        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        return rtrim((string) $settings->base_url, '/') . '/' . ltrim($endpoint, '/');
    }
}
