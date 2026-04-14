<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->enum('store', ['Physical', 'Online'])->default('Physical')->after('phone_number');
            $table->integer('rating')->nullable()->after('invoice_number');
            $table->unsignedBigInteger('staff_id')->nullable()->after('rating');
            
            // Note: We'll handle the department enum change in the model/logic 
            // to avoid migration complexities with enum changes in SQLite/MySQL
        });
        
        // Convert existing 'Online' departments to 'Retail' and set store to 'Online'
        \DB::table('feedbacks')->where('department', 'Online')->update([
            'department' => 'Retail',
            'store' => 'Online'
        ]);
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropColumn(['store', 'rating', 'staff_id']);
        });
    }
};
