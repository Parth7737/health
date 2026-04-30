<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('opd_prescriptions', function (Blueprint $table) {
            $table->string('prescription_no', 40)->nullable()->after('doctor_id');
            $table->unique(['hospital_id', 'prescription_no'], 'opd_presc_hospital_rxno_unique');
        });

        Schema::table('ipd_prescriptions', function (Blueprint $table) {
            $table->string('prescription_no', 40)->nullable()->after('doctor_id');
            $table->unique(['hospital_id', 'prescription_no'], 'ipd_presc_hospital_rxno_unique');
        });

        DB::table('opd_prescriptions')
            ->whereNull('prescription_no')
            ->orderBy('id')
            ->select(['id', 'created_at'])
            ->get()
            ->each(function ($row) {
                $date = $row->created_at ? date('ym', strtotime((string) $row->created_at)) : date('ym');
                DB::table('opd_prescriptions')
                    ->where('id', $row->id)
                    ->update([
                        'prescription_no' => 'OPD-RX-' . $date . '-' . str_pad((string) $row->id, 5, '0', STR_PAD_LEFT),
                    ]);
            });

        DB::table('ipd_prescriptions')
            ->whereNull('prescription_no')
            ->orderBy('id')
            ->select(['id', 'created_at'])
            ->get()
            ->each(function ($row) {
                $date = $row->created_at ? date('ym', strtotime((string) $row->created_at)) : date('ym');
                DB::table('ipd_prescriptions')
                    ->where('id', $row->id)
                    ->update([
                        'prescription_no' => 'IPD-RX-' . $date . '-' . str_pad((string) $row->id, 5, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_prescriptions', function (Blueprint $table) {
            $table->dropUnique('opd_presc_hospital_rxno_unique');
            $table->dropColumn('prescription_no');
        });

        Schema::table('ipd_prescriptions', function (Blueprint $table) {
            $table->dropUnique('ipd_presc_hospital_rxno_unique');
            $table->dropColumn('prescription_no');
        });
    }
};
