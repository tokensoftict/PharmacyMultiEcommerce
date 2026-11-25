<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('opay_webhook_logs')) {
            return;
        }

        Schema::table('opay_webhook_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('opay_webhook_logs', 'kafka_topic')) {
                $table->string('kafka_topic', 64)->nullable()->after('processed_at');
            }
            if (! Schema::hasColumn('opay_webhook_logs', 'kafka_status')) {
                $table->string('kafka_status', 32)->nullable()->index()->after('kafka_topic');
            }
            if (! Schema::hasColumn('opay_webhook_logs', 'kafka_forwarded_at')) {
                $table->timestamp('kafka_forwarded_at')->nullable()->after('kafka_status');
            }
            if (! Schema::hasColumn('opay_webhook_logs', 'kafka_error')) {
                $table->text('kafka_error')->nullable()->after('kafka_forwarded_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('opay_webhook_logs')) {
            return;
        }

        Schema::table('opay_webhook_logs', function (Blueprint $table) {
            $columns = array_filter(
                ['kafka_topic', 'kafka_status', 'kafka_forwarded_at', 'kafka_error'],
                static fn (string $col): bool => Schema::hasColumn('opay_webhook_logs', $col)
            );
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
