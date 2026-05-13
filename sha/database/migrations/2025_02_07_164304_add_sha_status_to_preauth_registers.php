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
            $table->json('sha_status')->nullable()->after('claim_aco_approved_date');
            $table->string('sha_remark')->nullable()->after('sha_status');
            $table->string('sha_status_added_by')->nullable()->after('sha_remark');
            $table->datetime('sha_status_update_date')->nullable()->after('sha_status_added_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['sha_status', 'sha_remark', 'sha_status_added_by', 'sha_status_update_date']);
        });
    }
};
