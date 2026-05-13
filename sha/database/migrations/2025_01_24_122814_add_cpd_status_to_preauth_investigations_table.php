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
            $table->string('cpd_status')->nullable();
            $table->datetime('cpd_status_verify_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->dropColumn(['cpd_status', 'cpd_status']);
        });
    }
};
