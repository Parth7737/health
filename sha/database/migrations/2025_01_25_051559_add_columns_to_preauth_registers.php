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
            $table->string('claim_query_supporting_doc')->nullable()->after('claim_approve_reject_query_by');
            $table->string('claim_query_add_doc')->nullable()->after('claim_query_supporting_doc');
            $table->text('claim_query_remarks')->nullable()->after('claim_query_add_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['claim_query_supporting_doc', 'claim_query_add_doc', 'claim_query_remarks']);
        });
    }
};
