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
        Schema::create('allergy_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allergy_id');
            $table->unsignedBigInteger('hospital_id');
            $table->string('name');
            $table->timestamps();
            
            $table->unique(['hospital_id', 'name']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('allergy_id')->references('id')->on('allergies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergy_reactions');
    }
};
