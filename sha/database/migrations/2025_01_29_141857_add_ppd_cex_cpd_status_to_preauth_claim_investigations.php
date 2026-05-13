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
        Schema::table('preauth_claim_investigations', function (Blueprint $table) {
            $table->string('ppd_status')->nullable()->after('updated_at');
            $table->datetime('ppd_status_verify_date')->nullable()->after('ppd_status');
            $table->string('cex_status')->nullable()->after('updated_at')->after('ppd_status_verify_date');
            $table->datetime('cex_status_verify_date')->nullable()->after('cex_status');
            $table->string('cpd_status')->nullable()->after('updated_at')->after('cex_status_verify_date');
            $table->datetime('cpd_status_verify_date')->nullable()->after('cpd_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_claim_investigations', function (Blueprint $table) {
            $table->dropColumn(['ppd_status', 'ppd_status_verify_date', 'cex_status', 'cex_status_verify_date', 'cpd_status', 'cpd_status_verify_date']);
        });
    }
};
