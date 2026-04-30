<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_opd_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_opd_charges', 'follow_up_charge')) {
                $table->decimal('follow_up_charge', 8, 2)->nullable()->after('charge');
            }

            if (!Schema::hasColumn('doctor_opd_charges', 'follow_up_validity_months')) {
                $table->unsignedInteger('follow_up_validity_months')->nullable()->after('follow_up_charge');
            }
        });

        Schema::table('opd_patients', function (Blueprint $table) {
            if (!Schema::hasColumn('opd_patients', 'consultation_case_type')) {
                $table->string('consultation_case_type', 50)->nullable()->after('applied_charge');
            }

            if (!Schema::hasColumn('opd_patients', 'consultation_case_label')) {
                $table->string('consultation_case_label')->nullable()->after('consultation_case_type');
            }

            if (!Schema::hasColumn('opd_patients', 'consultation_charge_source')) {
                $table->string('consultation_charge_source')->nullable()->after('consultation_case_label');
            }

            if (!Schema::hasColumn('opd_patients', 'consultation_reference_opd_patient_id')) {
                $table->unsignedBigInteger('consultation_reference_opd_patient_id')->nullable()->after('consultation_charge_source');
            }

            if (!Schema::hasColumn('opd_patients', 'consultation_valid_until')) {
                $table->dateTime('consultation_valid_until')->nullable()->after('consultation_reference_opd_patient_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctor_opd_charges', function (Blueprint $table) {
            foreach (['follow_up_charge', 'follow_up_validity_months'] as $column) {
                if (Schema::hasColumn('doctor_opd_charges', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('opd_patients', function (Blueprint $table) {
            foreach ([
                'consultation_case_type',
                'consultation_case_label',
                'consultation_charge_source',
                'consultation_reference_opd_patient_id',
                'consultation_valid_until',
            ] as $column) {
                if (Schema::hasColumn('opd_patients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};