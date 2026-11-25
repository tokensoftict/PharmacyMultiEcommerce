<?php

namespace App\Services\Opay;

use App\Enums\KafkaAction;
use App\Enums\KafkaEvent;
use App\Enums\KafkaTopics;
use App\Models\OpayWebhookLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Throwable;

/**
 * Publishes every OPay webhook received by `mystore` to Kafka so that
 * `PSGeneralDrugCentreKafka` consumes and stores it locally.
 */
class OpayWebhookKafkaPublisher
{
    /**
     * Build the normalized Kafka message body for an OPay webhook.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $headers
     * @return array{action: string, webhook: array<string, mixed>}
     */
    public function buildPayload(array $data, array $headers = [], ?OpayWebhookLog $log = null): array
    {
        return [
            'action' => KafkaAction::OPAY_WEBHOOK,
            'webhook' => [
                'mystore_webhook_log_id' => $log?->id,
                'transaction_id' => $headers['x-opay-tranid'] ?? ($log?->transaction_id ?? ($data['payNo'] ?? null)),
                'head_merchant_id' => isset($data['headMerchantId']) ? (string) $data['headMerchantId'] : null,
                'merchant_id' => $headers['merchantid'] ?? ($log?->merchant_id ?? (isset($data['merchantId']) ? (string) $data['merchantId'] : null)),
                'out_order_no' => isset($data['outOrderNo']) ? (string) $data['outOrderNo'] : $log?->out_order_no,
                'order_no' => isset($data['orderNo']) ? (string) $data['orderNo'] : $log?->order_no,
                'pay_no' => isset($data['payNo']) ? (string) $data['payNo'] : $log?->pay_no,
                'status' => isset($data['status']) ? (string) $data['status'] : $log?->status,
                'error_msg' => isset($data['errorMsg']) ? (string) $data['errorMsg'] : null,
                'amount' => isset($data['amount']) ? (string) $data['amount'] : $log?->amount,
                'fee' => isset($data['fee']) ? (string) $data['fee'] : null,
                'fee_pattern' => isset($data['feePattern']) ? (string) $data['feePattern'] : null,
                'settlement_amount' => isset($data['settlementAmount']) ? (string) $data['settlementAmount'] : null,
                'settlement_fee' => isset($data['settlementFee']) ? (string) $data['settlementFee'] : null,
                'settlement_fee_pattern' => isset($data['settlementFeePattern']) ? (string) $data['settlementFeePattern'] : null,
                'currency' => isset($data['currency']) ? (string) $data['currency'] : ($log?->currency ?: 'NGN'),
                'pay_method' => isset($data['payMethod']) ? (string) $data['payMethod'] : null,
                'sender_name' => isset($data['senderName']) ? (string) $data['senderName'] : null,
                'sender_bank' => isset($data['senderBank']) ? (string) $data['senderBank'] : null,
                'sender_account' => isset($data['senderAccount']) ? (string) $data['senderAccount'] : null,
                'recipient_name' => isset($data['recipientName']) ? (string) $data['recipientName'] : null,
                'receipt_bank' => isset($data['receiptBank']) ? (string) $data['receiptBank'] : null,
                'receipt_account' => isset($data['receiptAccount']) ? (string) $data['receiptAccount'] : null,
                'terminal_sn' => isset($data['sn']) ? (string) $data['sn'] : null,
                'remark' => isset($data['remark']) ? (string) $data['remark'] : null,
                'is_split' => isset($data['isSplit']) ? (string) $data['isSplit'] : null,
                'split_info' => isset($data['splitInfo']) && is_array($data['splitInfo']) ? $data['splitInfo'] : null,
                'product_info' => isset($data['productInfo']) ? (is_array($data['productInfo']) ? json_encode($data['productInfo']) : (string) $data['productInfo']) : null,
                'additional_information' => isset($data['additionalInformation']) ? (is_array($data['additionalInformation']) ? json_encode($data['additionalInformation']) : (string) $data['additionalInformation']) : null,
                'transaction_time' => isset($data['transactionTime']) ? (string) $data['transactionTime'] : null,
                'completed_time' => isset($data['completedTime']) ? (string) $data['completedTime'] : null,
                'signature_verified' => $log?->signature_verified,
                'source_ip' => $log?->source_ip,
                'received_at' => ($log?->created_at ?? now())->toIso8601String(),
                'headers' => $headers,
                'raw_payload' => $data,
            ],
        ];
    }

    /**
     * Publish an OPay webhook payload to Kafka and record delivery status on `$log`.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $headers
     */
    public function publish(array $data, array $headers = [], ?OpayWebhookLog $log = null): bool
    {
        if (! config('opay.kafka.enabled', true)) {
            return false;
        }

        $topic = (string) config('opay.kafka.topic', KafkaTopics::OPAY_KAFKA_TOPIC);
        $body = $this->buildPayload($data, $headers, $log);

        $message = new Message(
            headers: [
                'event' => KafkaEvent::ONLINE_PUSH,
                'action' => KafkaAction::OPAY_WEBHOOK,
                'source' => 'mystore-opay-webhook',
            ],
            body: $body,
            key: (string) config('app.KAFKA_HEADER_KEY', 'PSGDC'),
        );

        try {
            Kafka::publish()
                ->onTopic($topic)
                ->withMessage($message)
                ->send();

            $this->updateLogStatus($log, $topic, 'sent', null);

            return true;
        } catch (Throwable $e) {
            Log::error('[OPay Webhook Kafka] Failed to publish webhook to Kafka', [
                'topic' => $topic,
                'webhook_log_id' => $log?->id,
                'out_order_no' => $body['webhook']['out_order_no'] ?? null,
                'order_no' => $body['webhook']['order_no'] ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->updateLogStatus($log, $topic, 'failed', $e->getMessage());

            return false;
        }
    }

    /**
     * Republish an existing `OpayWebhookLog` record to Kafka.
     */
    public function republish(OpayWebhookLog $log): bool
    {
        return $this->publish(
            data: is_array($log->payload) ? $log->payload : [],
            headers: is_array($log->headers) ? $log->headers : [],
            log: $log,
        );
    }

    private function updateLogStatus(?OpayWebhookLog $log, string $topic, string $status, ?string $error): void
    {
        if (! $log || ! $log->exists || ! Schema::hasColumn('opay_webhook_logs', 'kafka_status')) {
            return;
        }

        try {
            $log->forceFill([
                'kafka_topic' => $topic,
                'kafka_status' => $status,
                'kafka_forwarded_at' => $status === 'sent' ? now() : $log->kafka_forwarded_at,
                'kafka_error' => $error,
            ])->save();
        } catch (Throwable $e) {
            Log::warning('[OPay Webhook Kafka] Could not update kafka_status on opay_webhook_logs: ' . $e->getMessage());
        }
    }
}
