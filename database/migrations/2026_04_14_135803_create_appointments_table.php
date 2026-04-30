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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('appointment_id')->unique();
            $table->string('name');
            $table->string('patient_phone');
            $table->string('patient_email')->nullable();
            $table->date('appointment_date');
            $table->string('appointment_slot')->nullable();
            $table->string('priority')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->integer('age')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->enum('status',['Pending', 'Confirmed', 'Cancelled'])->default('Pending');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('staff')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
