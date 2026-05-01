<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('diagnostic_order_items', 'clinical_indication')) {
                $table->text('clinical_indication')->nullable()->after('report_impression');
            }
            if (! Schema::hasColumn('diagnostic_order_items', 'report_technique')) {
                $table->text('report_technique')->nullable()->after('clinical_indication');
            }
            if (! Schema::hasColumn('diagnostic_order_items', 'report_radiologist_id')) {
                $table->unsignedBigInteger('report_radiologist_id')->nullable()->after('report_technique');
            }
            if (! Schema::hasColumn('diagnostic_order_items', 'report_category')) {
                $table->string('report_category', 64)->nullable()->after('report_radiologist_id');
            }
            if (! Schema::hasColumn('diagnostic_order_items', 'report_is_draft')) {
                $table->boolean('report_is_draft')->default(false)->after('report_category');
            }
        });

        if (Schema::hasTable('diagnostic_order_items')) {
            try {
                DB::statement("ALTER TABLE diagnostic_order_items MODIFY COLUMN status ENUM(
                    'ordered',
                    'sample_collected',
                    'in_progress',
                    'examination',
                    'completed',
                    'cancelled'
                ) NOT NULL DEFAULT 'ordered'");
            } catch (\Throwable $e) {
                // SQLite / non-MySQL: skip enum alteration
            }

            try {
                DB::table('diagnostic_order_items')
                    ->where('department', 'radiology')
                    ->where('status', 'in_progress')
                    ->update(['status' => 'examination']);
            } catch (\Throwable $e) {
                //
            }
        }

    }

    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            foreach (['report_is_draft', 'report_category', 'report_radiologist_id', 'report_technique', 'clinical_indication'] as $col) {
                if (Schema::hasColumn('diagnostic_order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        try {
            DB::table('diagnostic_order_items')
                ->where('department', 'radiology')
                ->where('status', 'examination')
                ->update(['status' => 'in_progress']);
        } catch (\Throwable $e) {
            //
        }

        try {
            DB::statement("ALTER TABLE diagnostic_order_items MODIFY COLUMN status ENUM(
                'ordered',
                'sample_collected',
                'in_progress',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'ordered'");
        } catch (\Throwable $e) {
            //
        }
    }
};
