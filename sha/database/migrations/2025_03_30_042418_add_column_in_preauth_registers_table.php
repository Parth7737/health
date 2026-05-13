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
            $table->string('erroneous_aco_remarks')->nullable()->after('erroneous_remarks');
            $table->string('erroneous_sha_remarks')->nullable()->after('erroneous_aco_remarks');
            $table->datetime('erroneous_claim_paid_date')->nullable()->after('erroneous_claim_approved_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            //
        });
    }
};
