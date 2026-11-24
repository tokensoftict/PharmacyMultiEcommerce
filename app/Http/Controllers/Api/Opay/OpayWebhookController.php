<?php

namespace App\Http\Controllers\Api\Opay;

use App\Events\Opay\OpayWebhookReceived;
use App\Http\Controllers\Controller;
use App\Models\OpaySetting;
use App\Models\OpayWebhookLog;
use App\Services\Opay\Exceptions\OpayCryptoException;
use App\Services\Opay\OpayRsaCrypto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Handles incoming OPay Offline / General Payment Webhook notifications.
 *
 * Per OPay documentation:
 *  - Must acknowledge within 5 seconds with HTTP 200 and:
 *    {"code": "00000", "message": "SUCCESSFUL"}
 *  - Supports both plaintext General Payment Webhooks (`{"data": {...}}`) and
 *    RSA-signed/encrypted payloads when `sign` and ciphertext `data` are sent.
 */
class OpayWebhookController extends Controller
{
    public function __construct(
        private readonly OpayRsaCrypto $crypto,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $rawBody = $request->all();
        $headers = [
            'x-opay-tranid' => $request->header('X-Opay-Tranid'),
            'merchantid' => $request->header('merchantId'),
            'content-type' => $request->header('Content-Type'),
        ];

        $signatureVerified = null;
        $data = [];

        // If OPay sends an encrypted & RSA-signed payload, verify and decrypt it first.
        if (isset($rawBody['sign']) && is_string($rawBody['data'] ?? null) && $rawBody['data'] !== '') {
            $settings = OpaySetting::current();

            if (! $settings->isConfigured()) {
                Log::warning('[OPay Webhook] Received encrypted/signed webhook before OPay keys were configured.');

                return response()->json([
                    'code' => '00004',
                    'message' => 'OPAY NOT CONFIGURED',
                ], 503);
            }

            try {
                $verified = $this->crypto->verifyAndDecryptResponse(
                    response: $rawBody,
                    opayPublicKey: (string) $settings->opay_public_key,
                    merchantPrivateKey: (string) $settings->merchant_private_key,
                    requireSuccessCode: false,
                );
                $signatureVerified = $verified['verified'];
                $data = is_array($verified['data']) ? $verified['data'] : [];
            } catch (OpayCryptoException $e) {
                Log::warning('[OPay Webhook] Signature/decryption verification failed', [
                    'error' => $e->getMessage(),
                    'ip' => $request->ip(),
                    'tran_id' => $headers['x-opay-tranid'],
                ]);

                return response()->json([
                    'code' => '00004',
                    'message' => 'INVALID SIGNATURE',
                ], 401);
            }
        } elseif (isset($rawBody['data']) && is_array($rawBody['data'])) {
            $data = $rawBody['data'];
        } elseif (is_array($rawBody)) {
            $data = $rawBody;
        }

        $logRecord = null;
        try {
            if (Schema::hasTable('opay_webhook_logs')) {
                $logRecord = OpayWebhookLog::create([
                    'transaction_id' => $headers['x-opay-tranid'] ?? ($data['payNo'] ?? null),
                    'merchant_id' => $headers['merchantid'] ?? ($data['merchantId'] ?? null),
                    'out_order_no' => isset($data['outOrderNo']) ? (string) $data['outOrderNo'] : null,
                    'order_no' => isset($data['orderNo']) ? (string) $data['orderNo'] : null,
                    'pay_no' => isset($data['payNo']) ? (string) $data['payNo'] : null,
                    'status' => isset($data['status']) ? (string) $data['status'] : null,
                    'amount' => isset($data['amount']) ? (string) $data['amount'] : null,
                    'currency' => isset($data['currency']) ? (string) $data['currency'] : null,
                    'signature_verified' => $signatureVerified,
                    'source_ip' => $request->ip(),
                    'headers' => $headers,
                    'payload' => $data,
                    'processed_at' => now(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('[OPay Webhook] Failed to persist webhook log: ' . $e->getMessage());
        }

        try {
            OpayWebhookReceived::dispatch($data, $headers, $logRecord);
        } catch (Throwable $e) {
            Log::error('[OPay Webhook] Event listener failed: ' . $e->getMessage());
        }

        return response()->json([
            'code' => '00000',
            'message' => 'SUCCESSFUL',
        ]);
    }
}
