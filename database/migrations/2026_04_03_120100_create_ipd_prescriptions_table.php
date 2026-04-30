<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('bed_allocation_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->longText('header_note')->nullable();
            $table->longText('footer_note')->nullable();
            $table->date('valid_till')->nullable();
            $table->json('notification_to')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'bed_allocation_id'], 'ipd_presc_hospital_allocation_idx');
            $table->index(['patient_id', 'created_at'], 'ipd_presc_patient_created_idx');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('bed_allocation_id')->references('id')->on('bed_allocations')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_prescriptions');
    }
};
