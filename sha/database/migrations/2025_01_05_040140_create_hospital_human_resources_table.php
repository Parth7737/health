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
        Schema::create('hospital_human_resources', function (Blueprint $table) {
            $table->id();   
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('healthcare_proffessionals_registry_id')->nullable();
            $table->string('type_of_human_resource');
            $table->bigInteger('sub_type_of_human_resource');
            $table->string('name');
            $table->string('email');
            $table->string('registration_number');
            $table->string('mobile_no');
            $table->text('registration_certificate');
            $table->text('declaration_certificate');
            $table->string('type')->nullable();
            $table->tinyInteger('is_added')->default(0);
            $table->tinyInteger('is_approve')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_human_resources');
    }
};
