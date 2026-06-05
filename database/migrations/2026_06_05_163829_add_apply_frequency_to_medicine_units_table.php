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
        Schema::table('medicine_units', function (Blueprint $table) {
            // If true, dispense qty = days × frequency no_of_medicine (e.g. Tab, Cap).
            // If false, dispense qty = 1 (e.g. Cream, Syrup, Powder — dispensed as whole pack).
            $table->boolean('apply_frequency')->default(true)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_units', function (Blueprint $table) {
            $table->dropColumn('apply_frequency');
        });
    }
};
