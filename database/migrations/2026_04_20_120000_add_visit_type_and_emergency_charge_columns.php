<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            if (!Schema::hasColumn('opd_patients', 'visit_type')) {
                $table->string('visit_type', 20)->nullable()->after('slot');
                $table->index('visit_type', 'opd_patients_visit_type_idx');
            }
        });

        Schema::table('doctor_opd_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_opd_charges', 'emergency_charge')) {
                $table->decimal('emergency_charge', 8, 2)->nullable()->after('follow_up_validity_months');
            }
        });
    }

    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            if (Schema::hasColumn('opd_patients', 'visit_type')) {
                try {
                    $table->dropIndex('opd_patients_visit_type_idx');
                } catch (\Throwable $e) {
                }
                $table->dropColumn('visit_type');
            }
        });

        Schema::table('doctor_opd_charges', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_opd_charges', 'emergency_charge')) {
                $table->dropColumn('emergency_charge');
            }
        });
    }
};
