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
        Schema::create('pathology_test_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pathology_test_id');
            $table->foreign('pathology_test_id')->references('id')->on('pathology_tests')->onDelete('cascade');
            $table->unsignedBigInteger('pathology_parameter_id');
            $table->foreign('pathology_parameter_id')->references('id')->on('pathology_parameters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathology_test_parameters');
    }
};
