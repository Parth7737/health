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
            $table->boolean('is_high_risk')
                ->default(false)
                ->after('description');

            $table->boolean('requires_rx')
                ->default(true)
                ->after('is_high_risk');

            $table->decimal('min_dose', 10, 2)
                ->nullable()
                ->after('requires_rx');

            $table->decimal('max_dose', 10, 2)
                ->nullable()
                ->after('min_dose');

            $table->decimal('max_daily_dose', 10, 2)
                ->nullable()
                ->after('max_dose');

            $table->string('dose_unit')
                ->nullable()
                ->after('max_daily_dose');

            $table->boolean('weight_based_dose')
                ->default(false)
                ->after('dose_unit');

            $table->decimal('dose_per_kg', 10, 2)
                ->nullable()
                ->after('weight_based_dose');

            $table->enum('pregnancy_risk', [
                'safe',
                'caution',
                'moderate',
                'high_risk',
                'contraindicated'
            ])->nullable()->after('dose_per_kg');

            $table->boolean('renal_adjustment_required')
                ->default(false)
                ->after('pregnancy_risk');

            $table->boolean('liver_adjustment_required')
                ->default(false)
                ->after('renal_adjustment_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'is_high_risk',
                'requires_rx',
                'min_dose',
                'max_dose',
                'max_daily_dose',
                'dose_unit',
                'weight_based_dose',
                'dose_per_kg',
                'pregnancy_risk',
                'renal_adjustment_required',
                'liver_adjustment_required'
            ]);
        });
    }
};
