<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('diagnostic_orders', 'ward_or_opd')) {
                $table->string('ward_or_opd', 120)->nullable()->after('doctor_staff_id');
            }
            if (! Schema::hasColumn('diagnostic_orders', 'contrast_required')) {
                $table->string('contrast_required', 40)->nullable()->after('ward_or_opd');
            }
            if (! Schema::hasColumn('diagnostic_orders', 'previous_relevant_imaging')) {
                $table->text('previous_relevant_imaging')->nullable()->after('contrast_required');
            }
            if (! Schema::hasColumn('diagnostic_orders', 'radiation_consent')) {
                $table->string('radiation_consent', 40)->nullable()->after('previous_relevant_imaging');
            }
            if (! Schema::hasColumn('diagnostic_orders', 'pregnancy_status')) {
                $table->string('pregnancy_status', 40)->nullable()->after('radiation_consent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_orders', function (Blueprint $table) {
            foreach ([
                'pregnancy_status',
                'radiation_consent',
                'previous_relevant_imaging',
                'contrast_required',
                'ward_or_opd',
            ] as $column) {
                if (Schema::hasColumn('diagnostic_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
