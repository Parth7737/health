<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->boolean('absent_flag')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropColumn('absent_flag');
        });
    }
};
