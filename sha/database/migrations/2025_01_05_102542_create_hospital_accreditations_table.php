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
        Schema::create('hospital_accreditations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('accreditation')->nullable();
            $table->integer('accreditation_id')->nullable();
            $table->string('certificate_no')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_till')->nullable();
            $table->string('certificate')->nullable();
            $table->text('speciality_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_accreditations');
    }
};
