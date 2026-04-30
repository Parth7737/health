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
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['dietary_id']);
            $table->json('dietary_id')->nullable()->after('religion_id');
            $table->json('allergy_id')->nullable()->after('dietary_id');
            $table->json('habit_id')->nullable()->after('allergy_id');
            $table->json('disease_id')->nullable()->after('habit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['dietary_id', 'allergy_id', 'habit_id', 'disease_id']);
            $table->unsignedBigInteger('dietary_id')->nullable()->after('religion_id');
        });
    }
};
