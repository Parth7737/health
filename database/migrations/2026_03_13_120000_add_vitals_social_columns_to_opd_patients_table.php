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
            $table->string('systolic_bp')->nullable()->after('bp');
            $table->string('diastolic_bp')->nullable()->after('systolic_bp');
            $table->string('diabetes')->nullable()->after('respiration');
            $table->string('bmi')->nullable()->after('diabetes');

            $table->json('social_known_allergies')->nullable()->after('bmi');
            $table->json('social_allergic_reactions')->nullable()->after('social_known_allergies');
            $table->string('occupation')->nullable()->after('social_allergic_reactions');
            $table->string('social_marital_status')->nullable()->after('occupation');
            $table->string('place_of_birth')->nullable()->after('social_marital_status');
            $table->string('current_location')->nullable()->after('place_of_birth');
            $table->string('years_in_current_location')->nullable()->after('current_location');
            $table->json('social_habits')->nullable()->after('years_in_current_location');
            $table->json('family_history')->nullable()->after('social_habits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropColumn([
                'systolic_bp',
                'diastolic_bp',
                'diabetes',
                'bmi',
                'body_area',
                'social_known_allergies',
                'social_allergic_reactions',
                'occupation',
                'social_marital_status',
                'place_of_birth',
                'current_location',
                'years_in_current_location',
                'social_habits',
                'family_history',
            ]);
        });
    }
};
