<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();

            $table->enum('app_type', ['android', 'ios'])->comment('Target platform');

            $table->string('version_name', 20)->comment('Human-readable version e.g. 1.15.0');
            $table->unsignedInteger('version_code')->comment('Numeric version code e.g. 115');

            $table->boolean('force_update')->default(false)->comment('If true, the app must update before continuing');
            $table->string('update_message')->default('A new version of the app is available. Please update to continue.');
            $table->string('store_url')->nullable()->comment('Play Store or App Store URL');

            $table->boolean('is_active')->default(true)->comment('Only the latest active record per app_type is used');

            $table->timestamps();

            // Composite index so we can quickly find the latest active record per platform
            $table->index(['app_type', 'is_active', 'version_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
