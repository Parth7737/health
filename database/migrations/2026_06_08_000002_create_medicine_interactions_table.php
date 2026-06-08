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
        Schema::create('medicine_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('interact_medicine_id');
            $table->enum('severity', [
                'minor',
                'moderate',
                'major',
                'critical'
            ]);
            $table->text('clinical_effect')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();

            $table->index('medicine_id');
            $table->index('interact_medicine_id');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('interact_medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_interactions');
    }
};
