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
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->text('deduction_reason')->nullable()->after('procedure_price');
            $table->double('deducted_amount',24,2)->default(0)->after('deduction_reason');
            $table->text('deduction_remarks')->nullable()->after('deducted_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            //
        });
    }
};
