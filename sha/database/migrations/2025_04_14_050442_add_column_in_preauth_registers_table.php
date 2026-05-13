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
            $table->text('ceo_status')->nullable()->after('committee_query_response_remarks');
            $table->integer('ceo_forwarded_by')->nullable()->after('ceo_status');
            $table->datetime('ceo_approved_date')->nullable()->after('ceo_forwarded_by');
            $table->double('ceo_approved_amount',24,2)->nullable()->after('ceo_approved_date');
            $table->text('ceo_remarks')->nullable()->after('ceo_approved_amount');
            $table->string('ceo_query_supporting_doc')->nullable()->after('ceo_remarks');
            $table->string('ceo_query_response_remarks')->nullable()->after('ceo_query_supporting_doc');
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
