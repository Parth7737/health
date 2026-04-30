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
        Schema::create('opd_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreignId('hr_department_id')->references('id')->on('hr_departments')->onDelete('cascade');
            $table->datetime('appointment_date');
            $table->string('case_no');
            $table->enum('casualty', ['Yes', 'No'])->default('No');
            $table->enum('mlc_patient', ['Yes', 'No'])->default('No');
            $table->foreignId('tpa_id')->references('id')->on('tpas')->onDelete('cascade');
            $table->string('tpa_reference_no')->nullable();
            $table->integer('symptoms_type_id')->nullable();
            $table->json('symptoms')->nullable();
            $table->text('symptoms_description')->nullable();
            $table->string('slot')->nullable();
            $table->decimal('standard_charge', 10, 2)->default(0);
            $table->decimal('applied_charge', 10, 2)->default(0);
            $table->enum('payment_mode', ['Cash', 'Card', 'Online'])->nullable();
            $table->enum('live_consultation', ['Yes', 'No'])->default('No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opd_patients');
    }
};
