<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('diagnostic_order_items', 'technician_remarks')) {
                $table->text('technician_remarks')->nullable()->after('report_text');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'pathologist_comment')) {
                $table->text('pathologist_comment')->nullable()->after('technician_remarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            $table->dropColumn(['technician_remarks', 'pathologist_comment']);
        });
    }
};
