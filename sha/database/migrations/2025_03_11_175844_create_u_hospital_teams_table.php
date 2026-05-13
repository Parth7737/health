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
        Schema::create('u_hospital_teams', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('hospital_id');
            $table->bigInteger('speciality_id');
            $table->string('name', 255)->nullable();
            $table->string('hpr_id', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->string('employement_type', 255)->nullable();
            $table->string('registration_no', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('mobile', 255)->nullable();
            $table->string('registration_certificate', 255)->nullable();
            $table->string('declaration_certificate', 255)->nullable();
            $table->date('declaration_certificate_expiry')->nullable();
            $table->date('registration_certificate_expiry')->nullable();
            $table->boolean('is_approve')->default(0);
            $table->string('dec_verify_status', 255)->nullable();
            $table->text('dec_verify_remark')->nullable();
            $table->bigInteger('dec_verify_id')->nullable();
            $table->string('dec_status', 255)->nullable();
            $table->text('dec_remark')->nullable();
            $table->bigInteger('dec_id')->nullable();
            $table->string('sec_status', 255)->nullable();
            $table->text('sec_remark')->nullable();
            $table->bigInteger('sec_id')->nullable();
            $table->bigInteger('main_hospitalid')->nullable();
            $table->bigInteger('old_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_hospital_teams');
    }
};
