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
        Schema::table('floors', function (Blueprint $table) {
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade')->after('hospital_id');

            $table->index(['hospital_id', 'building_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropIndex(['hospital_id', 'building_id']);
            $table->dropColumn('building_id');
        });
    }
};
