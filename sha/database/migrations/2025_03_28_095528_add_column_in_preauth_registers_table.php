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
            $table->string('erroneous_raise_supporting_doc')->nullable()->after('claim_other_doc');
            $table->text('erroneous_raise_remarks')->nullable()->after('erroneous_raise_supporting_doc');
            $table->double('erroneous_raise_amount',24,2)->nullable()->after('erroneous_raise_remarks');
            $table->text('erroneous_remarks')->nullable()->after('erroneous_raise_amount');
            $table->integer('erroneous_claim_approve_reject_query_by')->nullable()->after('erroneous_remarks');
            $table->double('erroneous_appoved_amount',24,2)->nullable()->after('erroneous_claim_approve_reject_query_by');
            $table->datetime('erroneous_claim_approved_date')->nullable()->after('erroneous_appoved_amount');
            $table->string('erroneous_query_supporting_doc')->nullable()->after('erroneous_claim_approved_date');
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
