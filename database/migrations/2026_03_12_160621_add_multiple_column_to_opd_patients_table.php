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
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->string('height')->nullable()->after('symptoms_description');
            $table->string('weight')->nullable()->after('height');
            $table->string('bp')->nullable()->after('weight');
            $table->string('pluse')->nullable()->after('bp');
            $table->string('temperature')->nullable()->after('pluse');
            $table->string('respiration')->nullable()->after('temperature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'bp', 'pluse', 'temperature', 'respiration']);
        });
    }
};
