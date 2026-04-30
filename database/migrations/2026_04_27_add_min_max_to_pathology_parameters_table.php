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
        Schema::table('pathology_parameters', function (Blueprint $table) {
            $table->decimal('min_value', 10, 4)->nullable()->after('range')->comment('Minimum normal value');
            $table->decimal('max_value', 10, 4)->nullable()->after('min_value')->comment('Maximum normal value');
            $table->decimal('critical_low', 10, 4)->nullable()->after('max_value')->comment('Critical low threshold');
            $table->decimal('critical_high', 10, 4)->nullable()->after('critical_low')->comment('Critical high threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathology_parameters', function (Blueprint $table) {
            $table->dropColumn(['min_value', 'max_value', 'critical_low', 'critical_high']);
        });
    }
};
