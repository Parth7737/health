<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('radiology_parameters', function (Blueprint $table) {
            $table->decimal('min_value', 10, 4)->nullable()->after('range')->comment('Minimum normal value (optional, for auto-flag)');
            $table->decimal('max_value', 10, 4)->nullable()->after('min_value')->comment('Maximum normal value');
            $table->decimal('critical_low', 10, 4)->nullable()->after('max_value')->comment('Critical low threshold');
            $table->decimal('critical_high', 10, 4)->nullable()->after('critical_low')->comment('Critical high threshold');
        });
    }

    public function down(): void
    {
        Schema::table('radiology_parameters', function (Blueprint $table) {
            $table->dropColumn(['min_value', 'max_value', 'critical_low', 'critical_high']);
        });
    }
};
