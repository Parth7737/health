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
        Schema::create('recoveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id')->nullable();
            $table->datetime('approved_date')->nullable();
            $table->double('recovery_amount',24,2)->nullable();
            $table->double('recovered_amount',24,2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('recovery_supporting_doc')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recoveries');
    }
};
