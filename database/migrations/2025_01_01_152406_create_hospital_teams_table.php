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
        Schema::create('hospital_teams', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('hospital_id');
            $table->integer('speciality_id');
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('employement_type')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('registration_certificate')->nullable();
            $table->string('declaration_certificate')->nullable();
            $table->date('declaration_certificate_expiry')->nullable();
            $table->date('registration_certificate_expiry')->nullable();
            $table->tinyInteger('is_approve')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_teams');
    }
};
