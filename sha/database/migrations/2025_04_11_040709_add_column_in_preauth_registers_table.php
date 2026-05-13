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
            $table->text('medical_committee_status')->nullable()->after('cpd_extra_details');
            $table->integer('committee_forwarded_by')->nullable()->after('claim_forwarded_by');
            $table->datetime('committee_approved_date')->nullable()->after('committee_forwarded_by');
            $table->double('committee_approved_amount',24,2)->nullable()->after('committee_approved_date');
            $table->text('committee_remarks')->nullable()->after('committee_approved_date');
            $table->string('committee_query_supporting_doc')->nullable()->after('committee_remarks');
            $table->string('committee_query_response_remarks')->nullable()->after('committee_query_supporting_doc');
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
