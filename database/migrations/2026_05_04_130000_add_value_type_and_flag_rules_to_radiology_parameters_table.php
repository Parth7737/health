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
        Schema::table('radiology_parameters', function (Blueprint $table) {
            $table->enum('value_type', ['numeric', 'ordinal', 'boolean'])
                ->default('numeric')
                ->after('applicable_gender')
                ->comment('Type of parameter result for flag generation');
            $table->json('flag_rules')
                ->nullable()
                ->after('critical_high_female')
                ->comment('Custom flag mapping for non-numeric value types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_parameters', function (Blueprint $table) {
            $table->dropColumn(['value_type', 'flag_rules']);
        });
    }
};
