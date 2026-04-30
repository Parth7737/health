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
        Schema::create('bed_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('bed_id')->constrained('beds')->onDelete('restrict');
            $table->foreignId('admitted_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->foreignId('discharged_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->dateTime('admission_date');
            $table->dateTime('discharge_date')->nullable();
            $table->string('admission_type')->default('emergency'); // emergency, planned, icu, etc.
            $table->text('admission_notes')->nullable();
            $table->text('discharge_notes')->nullable();
            $table->string('discharge_status')->nullable(); // recovered, expired, absconded, etc.
            $table->timestamps();

            $table->index(['hospital_id', 'patient_id']);
            $table->index('bed_id');
            $table->index('admission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_allocations');
    }
};
