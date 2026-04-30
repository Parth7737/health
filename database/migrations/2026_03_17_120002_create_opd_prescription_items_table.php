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
        Schema::create('opd_prescription_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opd_prescription_id');
            $table->unsignedBigInteger('medicine_id')->nullable();
            $table->unsignedBigInteger('medicine_category_id')->nullable();
            $table->unsignedBigInteger('medicine_dosage_id')->nullable();
            $table->unsignedBigInteger('medicine_instruction_id')->nullable();
            $table->unsignedBigInteger('medicine_frequency_id')->nullable();
            $table->unsignedInteger('no_of_day')->nullable();
            $table->timestamps();

            $table->foreign('opd_prescription_id')->references('id')->on('opd_prescriptions')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('set null');
            $table->foreign('medicine_category_id')->references('id')->on('medicine_categories')->onDelete('set null');
            $table->foreign('medicine_dosage_id')->references('id')->on('medicine_dosages')->onDelete('set null');
            $table->foreign('medicine_instruction_id')->references('id')->on('medicine_instructions')->onDelete('set null');
            $table->foreign('medicine_frequency_id')->references('id')->on('medicine_frequencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opd_prescription_items');
    }
};
