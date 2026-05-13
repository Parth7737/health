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
            $table->text('acs_status')->nullable()->after('ceo_query_response_remarks');
            $table->integer('acs_forwarded_by')->nullable()->after('acs_status');
            $table->datetime('acs_approved_date')->nullable()->after('acs_forwarded_by');
            $table->double('acs_approved_amount',24,2)->nullable()->after('acs_approved_date');
            $table->text('acs_remarks')->nullable()->after('acs_approved_amount');
            $table->string('acs_query_supporting_doc')->nullable()->after('acs_remarks');
            $table->string('acs_query_response_remarks')->nullable()->after('acs_query_supporting_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['acs_status', 'acs_forwarded_by', 'acs_approved_date', 'acs_approved_amount', 'acs_remarks', 'acs_query_supporting_doc', 'acs_query_response_remarks']);
        });
    }
};
