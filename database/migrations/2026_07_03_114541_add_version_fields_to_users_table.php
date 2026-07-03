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
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_type', 50)->nullable()->after('last_seen')->comment('Device type of the user e.g. android, ios');
            $table->string('version', 20)->nullable()->after('device_type')->comment('App version e.g. 1.14');
            $table->unsignedInteger('version_code')->nullable()->after('version')->comment('App version code e.g. 114');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'version', 'version_code']);
        });
    }
};
