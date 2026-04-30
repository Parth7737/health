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
            $table->enum('applicable_gender', ['all', 'male', 'female'])->default('all')->after('critical_high')->comment('Gender for which this range applies');
            $table->decimal('min_value_male', 10, 4)->nullable()->after('applicable_gender')->comment('Minimum normal value for males');
            $table->decimal('max_value_male', 10, 4)->nullable()->after('min_value_male')->comment('Maximum normal value for males');
            $table->decimal('critical_low_male', 10, 4)->nullable()->after('max_value_male')->comment('Critical low for males');
            $table->decimal('critical_high_male', 10, 4)->nullable()->after('critical_low_male')->comment('Critical high for males');
            $table->decimal('min_value_female', 10, 4)->nullable()->after('critical_high_male')->comment('Minimum normal value for females');
            $table->decimal('max_value_female', 10, 4)->nullable()->after('min_value_female')->comment('Maximum normal value for females');
            $table->decimal('critical_low_female', 10, 4)->nullable()->after('max_value_female')->comment('Critical low for females');
            $table->decimal('critical_high_female', 10, 4)->nullable()->after('critical_low_female')->comment('Critical high for females');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathology_parameters', function (Blueprint $table) {
            $table->dropColumn(['applicable_gender', 'min_value_male', 'max_value_male', 'critical_low_male', 'critical_high_male', 'min_value_female', 'max_value_female', 'critical_low_female', 'critical_high_female']);
        });
    }
};
