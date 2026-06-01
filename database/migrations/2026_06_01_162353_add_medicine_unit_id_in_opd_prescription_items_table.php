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
        Schema::table('opd_prescription_items', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_unit_id')->nullable()->after('medicine_category_id');
            $table->foreign('medicine_unit_id')->references('id')->on('medicine_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_prescription_items', function (Blueprint $table) {
            $table->dropForeign(['medicine_unit_id']);
            $table->dropColumn('medicine_unit_id');
        });
    }
};
