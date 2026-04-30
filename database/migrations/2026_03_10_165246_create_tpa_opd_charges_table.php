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
        Schema::create('tpa_opd_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreignId('doctor_opd_charge_id')->references('id')->on('doctor_opd_charges')->onDelete('cascade');
            $table->foreignId('tpa_id')->references('id')->on('tpas')->onDelete('cascade');
            $table->decimal('charge', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpa_opd_charges');
    }
};
