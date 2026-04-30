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
        Schema::create('radiology_test_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('radiology_test_id');
            $table->foreign('radiology_test_id')->references('id')->on('radiology_tests')->onDelete('cascade');
            $table->unsignedBigInteger('radiology_parameter_id');
            $table->foreign('radiology_parameter_id')->references('id')->on('radiology_parameters')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['radiology_test_id', 'radiology_parameter_id'], 'radiology_test_param_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_test_parameters');
    }
};
