<?php

namespace Tests\Unit;

use App\Services\Opay\Exceptions\OpayApiException;
use App\Services\Opay\Exceptions\OpayCryptoException;
use App\Services\Opay\OpayRsaCrypto;
use PHPUnit\Framework\TestCase;

class OpayRsaCryptoTest extends TestCase
{
    private OpayRsaCrypto $crypto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crypto = new OpayRsaCrypto();
    }

    public function test_it_generates_valid_1024_and_2048_bit_rsa_key_pairs(): void
    {
        foreach ([1024, 2048] as $bits) {
            $pair = $this->crypto->generateKeyPair($bits);

            $this->assertSame($bits, $pair['bits']);
            $this->assertStringContainsString('-----BEGIN PUBLIC KEY-----', $pair['public_key_pem']);
            $this->assertStringContainsString('-----BEGIN ', $pair['private_key_pem']);
            $this->assertStringNotContainsString('-----', $pair['public_key_raw']);
            $this->assertStringNotContainsString("\n", $pair['public_key_raw']);
            $this->assertTrue($this->crypto->isValidPublicKey($pair['public_key_pem']));
            $this->assertTrue($this->crypto->isValidPublicKey($pair['public_key_raw']));
            $this->assertTrue($this->crypto->isValidPrivateKey($pair['private_key_pem']));
            $this->assertTrue($this->crypto->validateKeyPair($pair['public_key_raw'], $pair['private_key_pem']));
        }
    }

    public function test_it_detects_mismatched_key_pair(): void
    {
        $pairA = $this->crypto->generateKeyPair(1024);
        $pairB = $this->crypto->generateKeyPair(1024);

        $this->assertFalse($this->crypto->validateKeyPair($pairA['public_key_raw'], $pairB['private_key_pem']));
    }

    public function test_it_encrypts_and_decrypts_multi_chunk_payloads_larger_than_117_bytes(): void
    {
        $pair = $this->crypto->generateKeyPair(1024);

        $payload = [
            'z_field' => str_repeat('OPay-multi-block-payload-segment-', 12),
            'a_field' => '256622092286390',
            'nested' => [
                'b' => 2,
                'a' => 1,
            ],
            'splitInfo' => [
                ['splitMerchantId' => '256623083111348', 'splitAmount' => '20.00'],
                ['splitMerchantId' => '256623083111349', 'splitAmount' => '30.00'],
            ],
        ];

        $canonical = $this->crypto->canonicalJson($payload);
        $this->assertGreaterThan(400, strlen($canonical));

        $encrypted = $this->crypto->encryptByPublicKey($canonical, $pair['public_key_raw']);
        $decrypted = $this->crypto->decryptByPrivateKey($encrypted, $pair['private_key_pem']);

        $this->assertSame($canonical, $decrypted);
        $this->assertSame(json_decode($canonical, true), json_decode($decrypted, true));
    }

    public function test_full_request_and_response_handshake_matches_opay_specification(): void
    {
        $merchantKeys = $this->crypto->generateKeyPair(1024);
        $opayKeys = $this->crypto->generateKeyPair(1024);

        $requestPayload = [
            'outOrderNo' => 'ORD-20261124-0001',
            'amount' => '2200.00',
            'currency' => 'NGN',
            'merchantId' => '256622092286391',
            'headMerchantId' => '256622092286390',
            'remark' => str_repeat('General Drug Centre POS Order ', 6),
        ];
        $timestamp = '1732446000000';

        // Client -> OPay: encrypt with OPay public key, sign (paramContent . timestamp) with Merchant private key
        $requestBody = $this->crypto->buildEncryptedRequest(
            payload: $requestPayload,
            timestamp: $timestamp,
            opayPublicKey: $opayKeys['public_key_raw'],
            merchantPrivateKey: $merchantKeys['private_key_pem'],
        );

        $this->assertArrayHasKey('paramContent', $requestBody);
        $this->assertArrayHasKey('sign', $requestBody);

        // Simulate OPay server verifying signature with Merchant public key & decrypting with OPay private key
        $this->assertTrue(
            $this->crypto->verify(
                $requestBody['paramContent'] . $timestamp,
                $requestBody['sign'],
                $merchantKeys['public_key_raw'],
            )
        );
        $serverDecrypted = json_decode(
            $this->crypto->decryptByPrivateKey($requestBody['paramContent'], $opayKeys['private_key_pem']),
            true
        );
        $this->assertSame($this->crypto->sortRecursively($requestPayload), $serverDecrypted);

        // Simulate OPay server building response: encrypt with Merchant public key, sign sorted fields with OPay private key
        $responsePlain = ['orderNo' => '20261124998765432101', 'status' => 'SUCCESS'];
        $responseDataEnc = $this->crypto->encryptByPublicKey(
            $this->crypto->canonicalJson($responsePlain),
            $merchantKeys['public_key_raw'],
        );
        $opayResponse = [
            'code' => '00000',
            'message' => 'SUCCESSFUL',
            'timestamp' => '1732446001234',
            'data' => $responseDataEnc,
        ];
        $signContent = $this->crypto->buildResponseSignatureContent($opayResponse);
        $this->assertSame(
            "code=00000&data={$responseDataEnc}&message=SUCCESSFUL&timestamp=1732446001234",
            $signContent
        );
        $opayResponse['sign'] = $this->crypto->sign($signContent, $opayKeys['private_key_pem']);

        // Client verifies OPay signature & decrypts data
        $parsed = $this->crypto->verifyAndDecryptResponse(
            response: $opayResponse,
            opayPublicKey: $opayKeys['public_key_raw'],
            merchantPrivateKey: $merchantKeys['private_key_pem'],
        );

        $this->assertTrue($parsed['verified']);
        $this->assertSame($responsePlain, $parsed['data']);
    }

    public function test_it_rejects_tampered_response_signature(): void
    {
        $merchantKeys = $this->crypto->generateKeyPair(1024);
        $opayKeys = $this->crypto->generateKeyPair(1024);

        $enc = $this->crypto->encryptByPublicKey('{"ok":true}', $merchantKeys['public_key_raw']);
        $response = [
            'code' => '00000',
            'message' => 'SUCCESSFUL',
            'timestamp' => '1732446001234',
            'data' => $enc,
        ];
        $response['sign'] = $this->crypto->sign(
            $this->crypto->buildResponseSignatureContent($response),
            $opayKeys['private_key_pem'],
        );

        // Tamper with message after signing
        $response['message'] = 'TAMPERED';

        $this->expectException(OpayCryptoException::class);
        $this->expectExceptionMessage('signature verification failed');

        $this->crypto->verifyAndDecryptResponse(
            response: $response,
            opayPublicKey: $opayKeys['public_key_raw'],
            merchantPrivateKey: $merchantKeys['private_key_pem'],
        );
    }

    public function test_it_throws_api_exception_when_opay_code_is_not_00000(): void
    {
        $merchantKeys = $this->crypto->generateKeyPair(1024);
        $opayKeys = $this->crypto->generateKeyPair(1024);

        try {
            $this->crypto->verifyAndDecryptResponse(
                response: [
                    'code' => 'C_1111',
                    'message' => 'Fail to query merchant key.',
                ],
                opayPublicKey: $opayKeys['public_key_raw'],
                merchantPrivateKey: $merchantKeys['private_key_pem'],
            );
            $this->fail('Expected OpayApiException was not thrown.');
        } catch (OpayApiException $e) {
            $this->assertSame('C_1111', $e->opayCode);
            $this->assertSame('Fail to query merchant key.', $e->opayMessage);
        }
    }

    public function test_it_decrypts_official_opay_java_demo_multi_chunk_ciphertext(): void
    {
        // Official 5-chunk (640-byte) RSA/ECB/PKCS1Padding ciphertext & raw Base64 PKCS#8 private key
        // from https://documentation.opayweb.com/doc/offline/authentication.html (Java demo).
        $merchantPrivateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKWC78k8C1L/2o2docGH2KGvvUyNgmEp5wRRT6+nRUQC2I4LthZV1YgQltP2UQWHYjo4RRQQdyza4hNWlzUBvpSr+wZLOOiT3F1PrGXqJUrQr3FAdU3WbZfK9OS/jPYJB6IO9udZ++bnFZUX5fUg6Jd5Ro+USzG66F5THHRcvqyDAgMBAAECgYBFdoOOcF3MUsb14m9N2uanTStkYouSndRv8nRAIzKlzY/3ZaDhFNsORPAYO2tbp6spBg6v0g1CTQbO2OuS+o1yCSmA2vrEU7TDUa3BD+ED5foXSXCjge1ROAl/UVhRyu+XbjORdc2wFVaddxMDOAY2FcsxxtudgzHUSxkHc3dZwQJBANWdO3sNtkY7b50EBbQzSV0gM7yCCsNrLT8VHoiPZTE0s05aVSx02MnEqP3aEWL4Or6BsOMmfji9/V2cQ/RM3eMCQQDGWkYc8vCR6opdfo0AV+LzfWVDK+OCiqgTBSsItUrlRQKPelUNfXg1lVL9FjCvlTkg881CO5Wrs4ctBDDZojjhAkA03JYNm5URyD+UoZwvvclX7FIhQiUbuvgVdkp6DNJD3QDKUPxm5fuH4szXFRHcZgVTg5CchbgSnqGGetcuK591AkAJcu94HZKcQOIKhibNZ2JmWwjh5EtYQqazEUH1jj1dIqZ2k0tN8zYwx0ZGzDHRi3hHO5YBm7kLjufXmyWAmJvBAkEAmEvSRZlalxFJ9eicdVw6didULQ0phywhSPhDejV29pBbka7sLp1lIWLsVP9E4mifNrP9Uzij9J/znTvTXwqS7g==';
        $encryptedData = 'T6AwT6d/1k+1IE5e0/gaSFIqYRoquOGs1A72EYx/neDwWDTF9cGzhU+VYrUsdiGUAb0exHT0b03fRUBdE8GAlGXB7GEskfJlvvEVWnq8/ci3ZeaV32T7pQNFPJaCNjgPGzXk3+edyXPQTCTLjcMJDPXWRyPV6nNYtIJA+gMMAqVLTHpV3RfA7imwhChYWfhBJXbPxP1gCZm1wLOAHk69mpncvtyDO2oVgwJg29yQ6IsO750BTA81Mbnf/8UlS/9JEvR20PLqOFl3TmNq4Up1CXWjs69Z0wFeKJXXsJ4z7qtJ+TPVVE33UK+PiZbGlYoJFFIErTweEEOYVWP8b5A5GRMgxqb0YP0ajYQBkyjsGU5DHsius6TIUzYwbFPfLpFRAh+h+tx+vBXGhwp+5t3hEMUGau2loJy29+iB3xs3KOEx3Cj43qYflELQom5HhyK268PuEK+VTDAaGhEZzaABGz/ODNz4LyNVouFl0OmE6kfBIyVrt1gsx3Zly3kJWmUVk0dAuZSKjUkkDgnunQGHApY2zZpd8LDFhDK18jU5XHxKAsQR6fQ9IcvZ4OPcbSNCLdscBlNo7cvK0tLetbwlvyl0tpe8GkN8RBcWrCk8h1JOmmD1GyqOERtCOLPR9PFwgEn/I2Vq2b2Y6vNYEJNTcX5pd5BapOf7GQbNbg8PKpI=';

        $decrypted = $this->crypto->decryptByPrivateKey($encryptedData, $merchantPrivateKey);
        $decoded = json_decode($decrypted, true);

        $this->assertIsArray($decoded);
        $this->assertSame('1693300624661761', $decoded['outOrderNo']);
        $this->assertSame('256623082511339', $decoded['merchantId']);
        $this->assertSame('256623082511337', $decoded['headMerchantId']);
        $this->assertSame('1000', $decoded['amount']);
        $this->assertSame('NGN', $decoded['currency']);
        $this->assertSame('256623083111348', $decoded['splitInfo'][0]['splitMerchantId']);
    }
}
