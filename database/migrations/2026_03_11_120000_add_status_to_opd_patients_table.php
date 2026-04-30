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
            $table->enum('status', ['waiting', 'in_room', 'completed', 'booking'])
                ->default('waiting')
                ->after('live_consultation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
