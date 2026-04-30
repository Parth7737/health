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
        Schema::table('diagnostic_order_item_parameters', function (Blueprint $table) {
            $table->enum('result_flag', ['normal', 'low', 'high', 'critical_low', 'critical_high'])->nullable()->after('result_value')->comment('Auto-generated flag based on ranges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_order_item_parameters', function (Blueprint $table) {
            $table->dropColumn('result_flag');
        });
    }
};
