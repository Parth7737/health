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
        Schema::create('pathology_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->unsignedBigInteger('pathology_category_id')->nullable();
            $table->foreign('pathology_category_id')->references('id')->on('pathology_categories')->onDelete('set null');
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->text('sample_type')->nullable();
            $table->string('method')->nullable();
            $table->text('report_days')->nullable();
            $table->text('description')->nullable();
            $table->decimal('standard_charge', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathology_tests');
    }
};
