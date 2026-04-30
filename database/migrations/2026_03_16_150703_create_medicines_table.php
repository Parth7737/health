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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('medicine_category_id')->nullable();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('company')->nullable();
            $table->string('unit')->nullable();
            $table->text('composition')->nullable();
            $table->integer('min_level')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('vat')->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('medicine_category_id')->references('id')->on('medicine_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
