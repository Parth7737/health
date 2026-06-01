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
        if (Schema::hasColumn('medicine_dosages', 'medicine_category_id')) {
            Schema::table('medicine_dosages', function (Blueprint $table) {
                $table->dropUnique(['medicine_category_id', 'dosage']);
            });
        }
        
        Schema::table('medicine_dosages', function (Blueprint $table) {
            
            $table->dropForeign(['medicine_category_id']);
            $table->dropColumn('medicine_category_id');
            $table->unsignedBigInteger('medicine_unit_id')->nullable();
            $table->foreign('medicine_unit_id')->references('id')->on('medicine_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_dosages', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_category_id')->nullable()->after('id');
            $table->foreign('medicine_category_id')->references('id')->on('medicine_categories');
            $table->dropForeign(['medicine_unit_id']);
            $table->dropColumn('medicine_unit_id');
        });
    }
};
