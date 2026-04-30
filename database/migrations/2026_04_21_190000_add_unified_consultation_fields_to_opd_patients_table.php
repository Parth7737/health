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
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->string('spo2')->nullable()->after('weight');
            $table->text('subjective_notes')->nullable()->after('symptoms_description');
            $table->text('objective_notes')->nullable()->after('subjective_notes');
            $table->text('assessment_notes')->nullable()->after('objective_notes');
            $table->text('plan_notes')->nullable()->after('assessment_notes');
            $table->text('patient_instructions')->nullable()->after('plan_notes');
            $table->date('follow_up_date')->nullable()->after('patient_instructions');
            $table->string('disposition')->nullable()->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropColumn([
                'spo2',
                'subjective_notes',
                'objective_notes',
                'assessment_notes',
                'plan_notes',
                'patient_instructions',
                'follow_up_date',
                'disposition',
            ]);
        });
    }
};
