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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->string('package_id')->nullable();
            $table->string('procedure_code_1')->nullable();
            $table->string('speciality_id')->nullable();
            $table->string('procedure_code_2')->nullable();
            $table->string('is_multiple_procedure')->nullable();
            $table->text('procedure_name')->nullable();
            $table->string('icd_code')->nullable();
            $table->double('price',24,2)->default(0);
            $table->string('stratification_criteria')->nullable();
            $table->string('no_of_stratification')->nullable();
            $table->string('implants_high_end_consumables')->nullable();
            $table->string('more_than_one_implant')->nullable();
            $table->string('special_conditions')->nullable();
            $table->string('reservation_public_hospitals')->nullable();
            $table->string('reservation_tertiary_hospitals')->nullable();
            $table->string('level_of_care')->nullable();
            $table->string('los')->nullable();
            $table->string('auto_approved')->nullable();
            $table->text('mandatory_documents_pre_auth')->nullable();
            $table->text('mandatory_documents_claim_processing')->nullable();
            $table->string('procedure_label')->nullable();
            $table->string('special_condition_pop_up')->nullable();
            $table->text('special_condition_pop_up_message')->nullable();
            $table->string('special_conditions_rule')->nullable();
            $table->text('special_conditions_rule_message')->nullable();
            $table->string('enhancement_applicable')->nullable();
            $table->string('medical_or_surgical')->nullable();
            $table->string('day_care_procedure')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
