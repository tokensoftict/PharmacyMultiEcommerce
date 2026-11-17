<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seasonal_campaigns')) {
            return;
        }

        Schema::table('seasonal_campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('seasonal_campaigns', 'audience_type')) {
                $table->string('audience_type', 24)->default('all_users')->after('priority');
            }
            if (! Schema::hasColumn('seasonal_campaigns', 'target_user_ids')) {
                $table->json('target_user_ids')->nullable()->after('audience_type');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('seasonal_campaigns')) {
            return;
        }

        Schema::table('seasonal_campaigns', function (Blueprint $table) {
            $cols = array_filter(
                ['audience_type', 'target_user_ids'],
                fn (string $c) => Schema::hasColumn('seasonal_campaigns', $c)
            );
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};

