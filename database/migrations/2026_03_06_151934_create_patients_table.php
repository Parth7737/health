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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->string('patient_id')->unique();
            $table->string('name');
            $table->string('guardian_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age_years')->nullable();
            $table->integer('age_months')->default(0);
            $table->string('country_code')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->foreignId('nationality_id')->nullable()->references('id')->on('nationalities')->onDelete('set null');
            $table->foreignId('religion_id')->nullable()->references('id')->on('religions')->onDelete('set null');
            $table->foreignId('dietary_id')->nullable()->references('id')->on('dietaries')->onDelete('set null');
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('address')->nullable();
            $table->text('known_allergies')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->enum('is_staff', ['Yes', 'No'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
