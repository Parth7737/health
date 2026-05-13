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
        Schema::create('u_hospital_human_resources', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('healthcare_proffessionals_registry_id', 255)->nullable();
            $table->string('type_of_human_resource', 255);
            $table->bigInteger('sub_type_of_human_resource');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('registration_number', 255);
            $table->string('mobile_no', 255);
            $table->text('registration_certificate');
            $table->text('declaration_certificate');
            $table->string('type', 255)->nullable();
            $table->tinyInteger('is_added')->default(0);
            $table->tinyInteger('is_approve')->default(0);
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
        Schema::dropIfExists('u_hospital_human_resources');
    }
};
