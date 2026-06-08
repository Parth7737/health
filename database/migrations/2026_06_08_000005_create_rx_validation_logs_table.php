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
        Schema::create('rx_validation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('prescription_id');
            $table->string('prescription_type'); // 'opd' or 'ipd'
            $table->unsignedBigInteger('prescription_item_id')->nullable();
            $table->unsignedBigInteger('medicine_id');
            $table->enum('validation_type', [
                'dose',
                'interaction',
                'allergy',
                'pregnancy',
                'duplicate_therapy',
                'high_risk'
            ]);
            $table->enum('severity', [
                'info',
                'minor',
                'moderate',
                'major',
                'critical'
            ]);
            $table->text('message');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'escalated'
            ])->default('pending');
            $table->unsignedBigInteger('action_by')->nullable();
            $table->text('action_note')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->timestamps();

            $table->index('prescription_id');
            $table->index('patient_id');
            $table->index('medicine_id');
            $table->index('status');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('action_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rx_validation_logs');
    }
};
