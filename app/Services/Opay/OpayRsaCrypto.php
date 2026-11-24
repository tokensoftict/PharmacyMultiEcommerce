<?php

namespace App\Services\Opay;

use App\Services\Opay\Exceptions\OpayApiException;
use App\Services\Opay\Exceptions\OpayCryptoException;
use OpenSSLAsymmetricKey;

/**
 * Cryptographic engine for OPay Offline Payments API authentication.
 *
 * Implements OPay's exact RSA + SHA256withRSA specification:
 *  - Request encryption: chunked RSA PKCS#1 v1.5 encryption using OPay's Public Key -> Base64 (`paramContent`)
 *  - Request signing:    SHA256withRSA over (`paramContent` . `timestamp`) using Merchant Private Key -> Base64 (`sign`)
 *  - Response verify:    SHA256withRSA over alphabetically sorted `key=value&...` (excluding `sign`) using OPay's Public Key
 *  - Response decrypt:   chunked RSA PKCS#1 v1.5 decryption of `data` using Merchant Private Key -> JSON decode
 *
 * Reference: https://documentation.opayweb.com/doc/offline/authentication.html
 */
class OpayRsaCrypto
{
    public const DEFAULT_KEY_BITS = 1024;
    public const PKCS1_PADDING_OVERHEAD = 11;

    /**
     * Generate a new RSA key pair compatible with OPay's Offline API.
     *
     * @return array{private_key_pem: string, public_key_pem: string, public_key_raw: string, bits: int}
     */
    public function generateKeyPair(int $bits = self::DEFAULT_KEY_BITS): array
    {
        if (! in_array($bits, [1024, 2048], true)) {
            throw new OpayCryptoException("Unsupported RSA key size: {$bits}. Use 1024 or 2048 bits.");
        }

        $this->drainOpenSslErrors();

        $resource = openssl_pkey_new([
            'private_key_bits' => $bits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if ($resource === false) {
            throw new OpayCryptoException('Failed to generate RSA key pair: ' . $this->lastOpenSslError());
        }

        $privateKeyPem = '';
        if (! openssl_pkey_export($resource, $privateKeyPem)) {
            throw new OpayCryptoException('Failed to export RSA private key: ' . $this->lastOpenSslError());
        }

        $details = openssl_pkey_get_details($resource);
        if (! is_array($details) || empty($details['key'])) {
            throw new OpayCryptoException('Failed to extract RSA public key: ' . $this->lastOpenSslError());
        }

        $publicKeyPem = trim((string) $details['key']);

        return [
            'private_key_pem' => trim($privateKeyPem),
            'public_key_pem' => $publicKeyPem,
            'public_key_raw' => self::stripPemHeaders($publicKeyPem),
            'bits' => (int) ($details['bits'] ?? $bits),
        ];
    }

    /**
     * Strip PEM header/footer lines and whitespace, producing the single-line
     * Base64 format accepted by OPay's Dashboard ("Merchant Public Key" field).
     */
    public static function stripPemHeaders(string $key): string
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($key)) ?: [];
        $body = array_filter(
            $lines,
            static fn (string $line): bool => ! str_starts_with(trim($line), '-----')
        );

        return preg_replace('/\s+/', '', implode('', $body)) ?? '';
    }

    /**
     * Normalize either raw Base64 or PEM public key text into valid PEM format.
     */
    public static function normalizePublicKeyToPem(string $key): string
    {
        $trimmed = trim(str_replace(["\r\n", "\r"], "\n", $key));

        if (str_contains($trimmed, '-----BEGIN ')) {
            return $trimmed;
        }

        $clean = preg_replace('/\s+/', '', $trimmed) ?? '';

        return "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split($clean, 64, "\n")
            . '-----END PUBLIC KEY-----';
    }

    /**
     * Normalize either raw Base64 or PEM private key text into valid PEM format.
     */
    public static function normalizePrivateKeyToPem(string $key, bool $pkcs1 = false): string
    {
        $trimmed = trim(str_replace(["\r\n", "\r"], "\n", $key));

        if (str_contains($trimmed, '-----BEGIN ')) {
            return $trimmed;
        }

        $clean = preg_replace('/\s+/', '', $trimmed) ?? '';
        $header = $pkcs1 ? 'RSA PRIVATE KEY' : 'PRIVATE KEY';

        return "-----BEGIN {$header}-----\n"
            . chunk_split($clean, 64, "\n")
            . "-----END {$header}-----";
    }

    /**
     * Validate that a public key string is parseable by OpenSSL and is RSA.
     */
    public function isValidPublicKey(string $publicKey): bool
    {
        try {
            $this->loadPublicKey($publicKey);

            return true;
        } catch (OpayCryptoException) {
            return false;
        }
    }

    /**
     * Validate that a private key string is parseable by OpenSSL and is RSA.
     */
    public function isValidPrivateKey(string $privateKey): bool
    {
        try {
            $this->loadPrivateKey($privateKey);

            return true;
        } catch (OpayCryptoException) {
            return false;
        }
    }

    /**
     * Verify that the given public and private keys are a mathematically matching pair.
     */
    public function validateKeyPair(string $publicKey, string $privateKey): bool
    {
        try {
            $probe = 'opay-keypair-probe-' . bin2hex(random_bytes(8));
            $signature = $this->sign($probe, $privateKey);

            return $this->verify($probe, $signature, $publicKey);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Recursively sort associative array keys alphabetically to match OPay's
     * canonical JSON serialization (`sort_keys=True` / `_traverseData`).
     */
    public function sortRecursively(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        if (array_is_list($data)) {
            return array_map(fn (mixed $item): mixed => $this->sortRecursively($item), $data);
        }

        ksort($data, SORT_STRING);
        foreach ($data as $key => $value) {
            $data[$key] = $this->sortRecursively($value);
        }

        return $data;
    }

    /**
     * Encode request data as compact canonical JSON matching OPay's Python/JS demos.
     */
    public function canonicalJson(array $payload): string
    {
        $sorted = $this->sortRecursively($payload);
        $json = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new OpayCryptoException('Failed to encode payload to JSON: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * Encrypt UTF-8 plaintext in chunks using RSA/ECB/PKCS1Padding and return Base64 ciphertext.
     *
     * Block size is derived dynamically from the RSA key bit-length:
     *  - 1024-bit key => 117-byte plaintext blocks (128-byte ciphertext blocks)
     *  - 2048-bit key => 245-byte plaintext blocks (256-byte ciphertext blocks)
     */
    public function encryptByPublicKey(string $plaintext, string $publicKey): string
    {
        $key = $this->loadPublicKey($publicKey);
        $maxChunk = $this->keyByteLength($key) - self::PKCS1_PADDING_OVERHEAD;

        $length = strlen($plaintext);
        $encryptedBinary = '';
        $offset = 0;

        if ($length === 0) {
            $this->drainOpenSslErrors();
            $chunkOut = '';
            if (! openssl_public_encrypt('', $chunkOut, $key, OPENSSL_PKCS1_PADDING)) {
                throw new OpayCryptoException('RSA public encryption failed: ' . $this->lastOpenSslError());
            }

            return base64_encode($chunkOut);
        }

        while ($offset < $length) {
            $chunk = substr($plaintext, $offset, $maxChunk);
            $chunkOut = '';
            $this->drainOpenSslErrors();

            if (! openssl_public_encrypt($chunk, $chunkOut, $key, OPENSSL_PKCS1_PADDING)) {
                throw new OpayCryptoException('RSA public encryption failed: ' . $this->lastOpenSslError());
            }

            $encryptedBinary .= $chunkOut;
            $offset += $maxChunk;
        }

        return base64_encode($encryptedBinary);
    }

    /**
     * Decrypt Base64-encoded chunked RSA/ECB/PKCS1Padding ciphertext using Merchant Private Key.
     */
    public function decryptByPrivateKey(string $ciphertextBase64, string $privateKey): string
    {
        $key = $this->loadPrivateKey($privateKey);
        $blockSize = $this->keyByteLength($key);

        $ciphertext = base64_decode($ciphertextBase64, true);
        if ($ciphertext === false || $ciphertext === '') {
            throw new OpayCryptoException('Invalid Base64 ciphertext provided for RSA decryption.');
        }

        $length = strlen($ciphertext);
        $decrypted = '';
        $offset = 0;

        while ($offset < $length) {
            $chunk = substr($ciphertext, $offset, $blockSize);
            $chunkOut = '';
            $this->drainOpenSslErrors();

            if (! openssl_private_decrypt($chunk, $chunkOut, $key, OPENSSL_PKCS1_PADDING)) {
                throw new OpayCryptoException('RSA private decryption failed: ' . $this->lastOpenSslError());
            }

            $decrypted .= $chunkOut;
            $offset += $blockSize;
        }

        return $decrypted;
    }

    /**
     * Generate a SHA256withRSA signature encoded as Base64.
     */
    public function sign(string $content, string $privateKey): string
    {
        $key = $this->loadPrivateKey($privateKey);
        $signature = '';
        $this->drainOpenSslErrors();

        if (! openssl_sign($content, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new OpayCryptoException('RSA SHA256 signing failed: ' . $this->lastOpenSslError());
        }

        return base64_encode($signature);
    }

    /**
     * Verify a Base64-encoded SHA256withRSA signature.
     */
    public function verify(string $content, string $signatureBase64, string $publicKey): bool
    {
        $signature = base64_decode($signatureBase64, true);
        if ($signature === false || $signature === '') {
            return false;
        }

        $key = $this->loadPublicKey($publicKey);
        $this->drainOpenSslErrors();

        $result = openssl_verify($content, $signature, $key, OPENSSL_ALGO_SHA256);

        if ($result === -1) {
            throw new OpayCryptoException('RSA SHA256 signature verification error: ' . $this->lastOpenSslError());
        }

        return $result === 1;
    }

    /**
     * Build the exact OPay encrypted & signed request body (`paramContent` & `sign`).
     *
     * @return array{paramContent: string, sign: string}
     */
    public function buildEncryptedRequest(
        array $payload,
        string $timestamp,
        string $opayPublicKey,
        string $merchantPrivateKey,
    ): array {
        $json = $this->canonicalJson($payload);
        $paramContent = $this->encryptByPublicKey($json, $opayPublicKey);
        $sign = $this->sign($paramContent . $timestamp, $merchantPrivateKey);

        return [
            'paramContent' => $paramContent,
            'sign' => $sign,
        ];
    }

    /**
     * Build the canonical `key=value&...` signing string from an OPay response payload.
     *
     * Matches OPay's JS (`_verfiySign`), Python (`signature_content`), and Java (`getSignContent`) demos:
     * keys sorted ascending, skipping `sign`, empty keys, and null values.
     */
    public function buildResponseSignatureContent(array $response): string
    {
        $filtered = [];

        foreach ($response as $key => $value) {
            $keyStr = (string) $key;
            if ($keyStr === '' || $keyStr === 'sign' || $value === null) {
                continue;
            }

            $filtered[$keyStr] = is_array($value)
                ? $this->canonicalJson($value)
                : (string) $value;
        }

        ksort($filtered, SORT_STRING);

        $parts = [];
        foreach ($filtered as $key => $value) {
            $parts[] = "{$key}={$value}";
        }

        return implode('&', $parts);
    }

    /**
     * Verify OPay's response code and SHA256withRSA signature, then decrypt `data`.
     *
     * @return array{verified: bool, data: array|null, raw_plaintext: string|null}
     */
    public function verifyAndDecryptResponse(
        array $response,
        string $opayPublicKey,
        string $merchantPrivateKey,
        bool $requireSuccessCode = true,
    ): array {
        $code = isset($response['code']) ? (string) $response['code'] : '';
        $message = isset($response['message']) ? (string) $response['message'] : 'Unknown OPay response';

        if ($requireSuccessCode && $code !== '00000') {
            throw new OpayApiException(
                "OPay API error [{$code}]: {$message}",
                opayCode: $code,
                opayMessage: $message,
                rawResponse: $response,
            );
        }

        $sign = isset($response['sign']) ? (string) $response['sign'] : '';
        if ($sign === '') {
            throw new OpayCryptoException('OPay response is missing the `sign` parameter.');
        }

        $signContent = $this->buildResponseSignatureContent($response);
        $verified = $this->verify($signContent, $sign, $opayPublicKey);

        if (! $verified) {
            throw new OpayCryptoException('OPay response signature verification failed.');
        }

        $encryptedData = $response['data'] ?? null;
        if ($encryptedData === null || $encryptedData === '') {
            return [
                'verified' => true,
                'data' => null,
                'raw_plaintext' => null,
            ];
        }

        if (is_array($encryptedData)) {
            return [
                'verified' => true,
                'data' => $encryptedData,
                'raw_plaintext' => json_encode($encryptedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: null,
            ];
        }

        $plaintext = $this->decryptByPrivateKey((string) $encryptedData, $merchantPrivateKey);
        $decoded = json_decode($plaintext, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new OpayCryptoException('Failed to decode decrypted OPay JSON payload: ' . json_last_error_msg());
        }

        return [
            'verified' => true,
            'data' => is_array($decoded) ? $decoded : ['value' => $decoded],
            'raw_plaintext' => $plaintext,
        ];
    }

    /**
     * Current UTC timestamp in milliseconds as required by OPay's `timestamp` header.
     */
    public static function currentTimestampMs(): string
    {
        return (string) (int) round(microtime(true) * 1000);
    }

    private function loadPublicKey(string $publicKey): OpenSSLAsymmetricKey
    {
        $pem = self::normalizePublicKeyToPem($publicKey);
        $this->drainOpenSslErrors();

        $key = openssl_pkey_get_public($pem);
        if ($key === false) {
            throw new OpayCryptoException('Invalid RSA public key: ' . $this->lastOpenSslError());
        }

        $details = openssl_pkey_get_details($key);
        if (! is_array($details) || ($details['type'] ?? null) !== OPENSSL_KEYTYPE_RSA) {
            throw new OpayCryptoException('Provided public key is not a valid RSA key.');
        }

        return $key;
    }

    private function loadPrivateKey(string $privateKey): OpenSSLAsymmetricKey
    {
        $pem = self::normalizePrivateKeyToPem($privateKey, pkcs1: false);
        $this->drainOpenSslErrors();

        $key = openssl_pkey_get_private($pem);

        if ($key === false && ! str_contains($privateKey, '-----BEGIN ')) {
            $pemPkcs1 = self::normalizePrivateKeyToPem($privateKey, pkcs1: true);
            $this->drainOpenSslErrors();
            $key = openssl_pkey_get_private($pemPkcs1);
        }

        if ($key === false) {
            throw new OpayCryptoException('Invalid RSA private key: ' . $this->lastOpenSslError());
        }

        $details = openssl_pkey_get_details($key);
        if (! is_array($details) || ($details['type'] ?? null) !== OPENSSL_KEYTYPE_RSA) {
            throw new OpayCryptoException('Provided private key is not a valid RSA key.');
        }

        return $key;
    }

    private function keyByteLength(OpenSSLAsymmetricKey $key): int
    {
        $details = openssl_pkey_get_details($key);
        $bits = (int) ($details['bits'] ?? 0);

        if ($bits <= 0 || ($bits % 8) !== 0) {
            throw new OpayCryptoException('Unable to determine RSA key length.');
        }

        return (int) ($bits / 8);
    }

    private function drainOpenSslErrors(): void
    {
        while (openssl_error_string() !== false) {
            // Drain queued errors so subsequent error reports reflect only the current call.
        }
    }

    private function lastOpenSslError(): string
    {
        $messages = [];
        while (($error = openssl_error_string()) !== false) {
            $messages[] = $error;
        }

        return $messages !== [] ? implode(' | ', $messages) : 'Unknown OpenSSL error';
    }
}
