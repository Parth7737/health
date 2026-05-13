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
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->json('cex_status')->nullable()->after('query_remarks');
            $table->json('cpd_status')->nullable()->after('cex_status');
            $table->date('cex_hospital_bill_date')->nullable()->after('cpd_status');
            $table->date('cex_admission_date')->nullable()->after('cex_hospital_bill_date');
            $table->date('cex_discharge_date')->nullable()->after('cex_admission_date');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['cex_status', 'cpd_status', 'cex_hospital_bill_date', 'cex_admission_date', 'cex_discharge_date']);           
        });
    }
};
