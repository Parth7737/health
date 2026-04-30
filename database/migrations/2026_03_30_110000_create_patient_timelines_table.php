<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('encounter_type', 20)->default('general');
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->string('event_key', 100);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'patient_id', 'logged_at'], 'patient_timelines_patient_idx');
            $table->index(['encounter_type', 'encounter_id'], 'patient_timelines_encounter_idx');
            $table->index('event_key', 'patient_timelines_event_key_idx');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_timelines');
    }
};
