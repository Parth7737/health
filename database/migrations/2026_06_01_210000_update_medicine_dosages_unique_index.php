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
        Schema::table('medicine_dosages', function (Blueprint $table) {
            $table->unique(['medicine_unit_id', 'dosage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('medicine_dosages', 'medicine_unit_id')) {
            Schema::table('medicine_dosages', function (Blueprint $table) {
                $table->dropUnique(['medicine_unit_id', 'dosage']);
            });
        }

        if (Schema::hasColumn('medicine_dosages', 'medicine_category_id')) {
            Schema::table('medicine_dosages', function (Blueprint $table) {
                $table->unique(['medicine_category_id', 'dosage']);
            });
        }
    }
};
