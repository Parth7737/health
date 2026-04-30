<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pathology_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('pathology_tests', 'charge_master_id')) {
                $table->unsignedBigInteger('charge_master_id')->nullable()->after('standard_charge');
                $table->index('charge_master_id', 'pathology_tests_charge_master_idx');
                $table->foreign('charge_master_id')->references('id')->on('charge_masters')->onDelete('set null');
            }
        });

        Schema::table('radiology_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('radiology_tests', 'charge_master_id')) {
                $table->unsignedBigInteger('charge_master_id')->nullable()->after('standard_charge');
                $table->index('charge_master_id', 'radiology_tests_charge_master_idx');
                $table->foreign('charge_master_id')->references('id')->on('charge_masters')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pathology_tests', function (Blueprint $table) {
            if (Schema::hasColumn('pathology_tests', 'charge_master_id')) {
                $table->dropForeign(['charge_master_id']);
                $table->dropIndex('pathology_tests_charge_master_idx');
                $table->dropColumn('charge_master_id');
            }
        });

        Schema::table('radiology_tests', function (Blueprint $table) {
            if (Schema::hasColumn('radiology_tests', 'charge_master_id')) {
                $table->dropForeign(['charge_master_id']);
                $table->dropIndex('radiology_tests_charge_master_idx');
                $table->dropColumn('charge_master_id');
            }
        });
    }
};
