<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_administration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('bed_allocation_id');
            $table->unsignedBigInteger('ipd_prescription_id');
            $table->unsignedBigInteger('ipd_prescription_item_id');
            $table->unsignedBigInteger('medicine_id');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->enum('meal_relation', [
                'none',
                'before_food',
                'after_food',
                'with_food',
                'empty_stomach',
            ])->default('none');
            $table->enum('status', [
                'pending',
                'given',
                'missed',
                'held',
                'refused',
            ])->default('pending');
            $table->timestamp('administered_at')->nullable();
            $table->unsignedBigInteger('administered_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['ipd_prescription_item_id', 'scheduled_date', 'scheduled_time'],
                'mar_item_date_time_unique'
            );
            $table->index(['hospital_id', 'scheduled_date', 'status'], 'mar_hospital_date_status_idx');
            $table->index(['bed_allocation_id', 'scheduled_date'], 'mar_bed_date_idx');
            $table->index('patient_id');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('bed_allocation_id')->references('id')->on('bed_allocations')->onDelete('cascade');
            $table->foreign('ipd_prescription_id')->references('id')->on('ipd_prescriptions')->onDelete('cascade');
            $table->foreign('ipd_prescription_item_id')->references('id')->on('ipd_prescription_items')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('administered_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_administration_logs');
    }
};
