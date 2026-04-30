<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('bed_allocations', 'admission_no')) {
                $table->string('admission_no')->nullable()->after('id');
                $table->index('admission_no', 'bed_allocations_admission_no_idx');
            }

            if (!Schema::hasColumn('bed_allocations', 'consultant_doctor_id')) {
                $table->foreignId('consultant_doctor_id')->nullable()->after('patient_id')->constrained('staff')->nullOnDelete();
            }

            if (!Schema::hasColumn('bed_allocations', 'hr_department_id')) {
                $table->foreignId('hr_department_id')->nullable()->after('consultant_doctor_id')->constrained('hr_departments')->nullOnDelete();
            }

            if (!Schema::hasColumn('bed_allocations', 'tpa_id')) {
                $table->foreignId('tpa_id')->nullable()->after('hr_department_id')->constrained('tpas')->nullOnDelete();
            }

            if (!Schema::hasColumn('bed_allocations', 'source_opd_patient_id')) {
                $table->foreignId('source_opd_patient_id')->nullable()->after('tpa_id')->constrained('opd_patients')->nullOnDelete();
            }

            if (!Schema::hasColumn('bed_allocations', 'admission_source')) {
                $table->string('admission_source', 30)->default('direct')->after('admission_type');
            }

            if (!Schema::hasColumn('bed_allocations', 'tpa_reference_no')) {
                $table->string('tpa_reference_no')->nullable()->after('admission_source');
            }

            if (!Schema::hasColumn('bed_allocations', 'expected_discharge_date')) {
                $table->dateTime('expected_discharge_date')->nullable()->after('discharge_date');
            }

            if (!Schema::hasColumn('bed_allocations', 'admission_reason')) {
                $table->text('admission_reason')->nullable()->after('admission_notes');
            }

            if (!Schema::hasColumn('bed_allocations', 'provisional_diagnosis')) {
                $table->text('provisional_diagnosis')->nullable()->after('admission_reason');
            }

            if (!Schema::hasColumn('bed_allocations', 'height')) {
                $table->string('height', 50)->nullable()->after('provisional_diagnosis');
            }

            if (!Schema::hasColumn('bed_allocations', 'weight')) {
                $table->string('weight', 50)->nullable()->after('height');
            }

            if (!Schema::hasColumn('bed_allocations', 'bp')) {
                $table->string('bp', 50)->nullable()->after('weight');
            }

            if (!Schema::hasColumn('bed_allocations', 'pulse')) {
                $table->string('pulse', 50)->nullable()->after('bp');
            }

            if (!Schema::hasColumn('bed_allocations', 'temperature')) {
                $table->string('temperature', 50)->nullable()->after('pulse');
            }

            if (!Schema::hasColumn('bed_allocations', 'respiration')) {
                $table->string('respiration', 50)->nullable()->after('temperature');
            }
        });

        Schema::table('opd_patients', function (Blueprint $table) {
            if (!Schema::hasColumn('opd_patients', 'ipd_admitted_at')) {
                $table->dateTime('ipd_admitted_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('opd_patients', 'ipd_bed_allocation_id')) {
                $table->unsignedBigInteger('ipd_bed_allocation_id')->nullable()->after('ipd_admitted_at');
                $table->index('ipd_bed_allocation_id', 'opd_patients_ipd_allocation_idx');
                $table->foreign('ipd_bed_allocation_id', 'opd_patients_ipd_allocation_fk')
                    ->references('id')
                    ->on('bed_allocations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            if (Schema::hasColumn('opd_patients', 'ipd_bed_allocation_id')) {
                $table->dropForeign('opd_patients_ipd_allocation_fk');
                $table->dropIndex('opd_patients_ipd_allocation_idx');
                $table->dropColumn('ipd_bed_allocation_id');
            }

            if (Schema::hasColumn('opd_patients', 'ipd_admitted_at')) {
                $table->dropColumn('ipd_admitted_at');
            }
        });

        Schema::table('bed_allocations', function (Blueprint $table) {
            foreach (['consultant_doctor_id', 'hr_department_id', 'tpa_id', 'source_opd_patient_id'] as $foreignColumn) {
                if (Schema::hasColumn('bed_allocations', $foreignColumn)) {
                    $table->dropConstrainedForeignId($foreignColumn);
                }
            }

            foreach ([
                'bed_allocations_admission_no_idx',
            ] as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (Throwable $e) {
                }
            }

            foreach ([
                'admission_no',
                'admission_source',
                'tpa_reference_no',
                'expected_discharge_date',
                'admission_reason',
                'provisional_diagnosis',
                'height',
                'weight',
                'bp',
                'pulse',
                'temperature',
                'respiration',
            ] as $column) {
                if (Schema::hasColumn('bed_allocations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};