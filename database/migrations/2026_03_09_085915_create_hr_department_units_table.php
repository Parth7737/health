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
        Schema::create('hr_department_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreignId('hr_department_id')->references('id')->on('hr_departments')->onDelete('cascade');
            $table->foreignId('floor_id')->nullable()->references('id')->on('floors')->onDelete('set null');
            $table->foreignId('unit_incharge_id')->nullable()->references('id')->on('staff')->onDelete('set null');
            $table->string('name');
            $table->enum('is_video_consultation', ['Yes', 'No'])->default('No');
            $table->integer('daily_capacity')->default(0);
            $table->unique(['hospital_id', 'hr_department_id', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_department_units');
    }
};
