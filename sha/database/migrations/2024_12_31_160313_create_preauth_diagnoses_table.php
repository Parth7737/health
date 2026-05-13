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
        Schema::create('preauth_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->integer('preauth_register_id');
            $table->integer('diagnosis_id')->nullable();
            $table->string('diagnosis_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preauth_diagnoses');
    }
};
