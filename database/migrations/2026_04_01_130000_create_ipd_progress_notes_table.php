<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_progress_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('bed_allocation_id');
            $table->unsignedBigInteger('patient_id');
            $table->enum('note_type', ['doctor', 'nursing', 'progress', 'discharge_plan'])->default('progress');
            $table->text('note');
            $table->timestamp('noted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'bed_allocation_id'], 'ipd_notes_hospital_allocation_idx');
            $table->index(['patient_id', 'noted_at'], 'ipd_notes_patient_noted_idx');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('bed_allocation_id')->references('id')->on('bed_allocations')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_progress_notes');
    }
};