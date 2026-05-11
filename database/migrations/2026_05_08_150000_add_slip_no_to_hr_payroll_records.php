<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_payroll_records', function (Blueprint $table) {
            $table->string('slip_no', 20)->nullable()->after('payroll_month');
            $table->unique(['hospital_id', 'slip_no'], 'hr_payroll_hospital_slip_unique');
        });
    }

    public function down(): void
    {
        Schema::table('hr_payroll_records', function (Blueprint $table) {
            $table->dropUnique('hr_payroll_hospital_slip_unique');
            $table->dropColumn('slip_no');
        });
    }
};
