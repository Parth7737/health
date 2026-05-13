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
        Schema::create('personal_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('preauth_register_id');
            $table->integer('appetite_id')->nullable();
            $table->integer('bowels_id')->nullable();
            $table->integer('nutrition_id')->nullable();
            $table->integer('diet_id')->nullable();
            $table->string('known_allergies')->nullable();
            $table->string('allergy_detail')->nullable();
            $table->string('habits')->nullable();
            $table->string('habits_detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_histories');
    }
};
