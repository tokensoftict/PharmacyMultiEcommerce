<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('opay_settings')) {
            Schema::create('opay_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_enabled')->default(false);
                $table->string('environment', 32)->default('live');
                $table->string('base_url', 255)->default('https://payapi.opayweb.com');
                $table->string('api_version', 32)->default('V1.0.1');
                $table->string('head_merchant_id', 64)->nullable();
                $table->string('merchant_id', 64)->nullable();
                $table->text('client_auth_key')->nullable();
                $table->text('opay_public_key')->nullable();
                $table->text('merchant_public_key')->nullable();
                $table->text('merchant_private_key')->nullable();
                $table->text('ip_whitelisting')->nullable();
                $table->string('webhook_url', 500)->nullable();
                $table->unsignedSmallInteger('key_bits')->default(1024);
                $table->timestamp('keys_generated_at')->nullable();
                $table->timestamp('last_tested_at')->nullable();
                $table->string('last_test_status', 32)->nullable();
                $table->text('last_test_message')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('opay_webhook_logs')) {
            Schema::create('opay_webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_id', 128)->nullable()->index();
                $table->string('merchant_id', 64)->nullable()->index();
                $table->string('out_order_no', 128)->nullable()->index();
                $table->string('order_no', 128)->nullable()->index();
                $table->string('pay_no', 128)->nullable();
                $table->string('status', 32)->nullable()->index();
                $table->string('amount', 32)->nullable();
                $table->string('currency', 16)->nullable();
                $table->boolean('signature_verified')->nullable();
                $table->string('source_ip', 64)->nullable();
                $table->json('headers')->nullable();
                $table->json('payload')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('opay_webhook_logs');
        Schema::dropIfExists('opay_settings');
    }
};
