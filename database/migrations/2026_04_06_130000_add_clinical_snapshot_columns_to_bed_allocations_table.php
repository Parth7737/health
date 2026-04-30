<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('bed_allocations', 'systolic_bp')) {
                $table->string('systolic_bp', 50)->nullable()->after('respiration');
            }

            if (!Schema::hasColumn('bed_allocations', 'diastolic_bp')) {
                $table->string('diastolic_bp', 50)->nullable()->after('systolic_bp');
            }

            if (!Schema::hasColumn('bed_allocations', 'diabetes')) {
                $table->string('diabetes', 50)->nullable()->after('diastolic_bp');
            }

            if (!Schema::hasColumn('bed_allocations', 'bmi')) {
                $table->string('bmi', 50)->nullable()->after('diabetes');
            }

            if (!Schema::hasColumn('bed_allocations', 'family_history')) {
                $table->text('family_history')->nullable()->after('bmi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            foreach (['systolic_bp', 'diastolic_bp', 'diabetes', 'bmi', 'family_history'] as $column) {
                if (Schema::hasColumn('bed_allocations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
