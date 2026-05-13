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
            $table->json('aco_observation_details')->nullable()->after('cex_discharge_date');
            $table->text('aco_remark')->nullable()->after('aco_observation_details');
            $table->bigInteger('aco_status_added_by')->nullable()->after('aco_remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['aco_observation_details', 'aco_remark', 'aco_status_added_by']);          
        });
    }
};
