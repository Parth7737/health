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
        Schema::create('opd_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('opd_patient_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->longText('header_note')->nullable();
            $table->longText('footer_note')->nullable();
            $table->date('valid_till')->nullable();
            $table->json('notification_to')->nullable();
            $table->timestamps();

            $table->unique('opd_patient_id');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('opd_patient_id')->references('id')->on('opd_patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opd_prescriptions');
    }
};
