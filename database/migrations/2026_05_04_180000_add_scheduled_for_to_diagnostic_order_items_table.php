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
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            $table->timestamp('scheduled_for')->nullable()->after('expected_report_days');
            $table->index(['department', 'scheduled_for'], 'diag_items_department_scheduled_for_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            $table->dropIndex('diag_items_department_scheduled_for_idx');
            $table->dropColumn('scheduled_for');
        });
    }
};
