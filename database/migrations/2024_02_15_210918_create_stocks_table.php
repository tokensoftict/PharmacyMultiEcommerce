<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('local_stock_id')->index();
            $table->string('name')->index()->nullable();
            $table->string('seo')->index()->nullable();
            $table->text("description")->nullable();
            $table->boolean('admin_status')->default(true);

            $table->foreignId("productcategory_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("manufacturer_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("classification_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("productgroup_id")->nullable()->constrained()->nullOnDelete();

            $table->bigInteger("wholesales")->default(0)->index();
            $table->bigInteger("retail")->default(0)->index();
            $table->bigInteger("quantity")->default(0)->index();
            $table->date('expiry_date')->nullable();

            $table->integer("piece")->default(0);
            $table->integer("box")->default(0);
            $table->integer("carton")->default(0);
            $table->boolean("sachet")->default(0);
            $table->string('image')->nullable();
            $table->boolean("is_wholesales")->default("0");
            $table->unsignedBigInteger("max")->default("0");
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('stocks');
        Schema::enableForeignKeyConstraints();
    }
};
