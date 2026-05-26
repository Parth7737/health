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
        Schema::table('opd_prescriptions', function (Blueprint $table) {
            $table->enum('dispense_type', ['Normal', 'Emergency'])->default('Normal')->after('footer_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_prescriptions', function (Blueprint $table) {
            $table->dropColumn('dispense_type');
        });
    }
};
