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
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->unsignedBigInteger('medicine_unit_id')->nullable()->after('company');
            $table->foreign('medicine_unit_id')->references('id')->on('medicine_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('company');
            $table->dropForeign(['medicine_unit_id']);
            $table->dropColumn('medicine_unit_id');
        });
    }
};
