<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pathology_sample_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('name');
            $table->timestamps();

            $table->unique(['hospital_id', 'name']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::create('pathology_test_sample_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pathology_test_id');
            $table->unsignedBigInteger('pathology_sample_type_id');
            $table->timestamps();

            $table->unique(['pathology_test_id', 'pathology_sample_type_id'], 'path_test_sample_type_unique');
            $table->foreign('pathology_test_id')->references('id')->on('pathology_tests')->onDelete('cascade');
            $table->foreign('pathology_sample_type_id')->references('id')->on('pathology_sample_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pathology_test_sample_types');
        Schema::dropIfExists('pathology_sample_types');
    }
};
