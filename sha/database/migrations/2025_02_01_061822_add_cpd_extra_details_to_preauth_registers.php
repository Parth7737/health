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
            $table->json('cpd_extra_details')->nullable()->after('query_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['cpd_extra_details']);           
        });
    }
};
