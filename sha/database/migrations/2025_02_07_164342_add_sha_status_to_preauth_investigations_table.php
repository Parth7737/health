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
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->string('sha_status')->nullable()->after('cpd_status_verify_date');
            $table->string('sha_status_verify_date')->nullable()->after('sha_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->dropColumn(['sha_status', 'sha_status_verify_date']);
        });
    }
};
