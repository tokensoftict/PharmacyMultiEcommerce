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
        Schema::create('supermarket_admins', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('status')->default(0);
            $table->boolean('invitation_status')->default(0);
            $table->date("invitation_sent_date")->nullable();
            $table->date('invitation_approval_date')->nullable();
            $table->text("token")->nullable();
            $table->string("code")->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->dateTime('last_activity_date')->nullable();
            $table->foreign('added_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
