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
        Schema::create('aadhaar_information', function (Blueprint $table) {
            $table->id();
            $table->string('aadhaar_no')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('care_of')->nullable();
            $table->text('full_address')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('email_hash')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('name')->nullable();
            $table->string('country')->nullable();
            $table->string('district')->nullable();
            $table->string('house')->nullable();
            $table->string('landmark')->nullable();
            $table->string('pincode')->nullable();
            $table->string('post_office')->nullable();
            $table->string('state')->nullable();
            $table->string('street')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('vtc')->nullable();
            $table->string('year_of_birth')->nullable();
            $table->string('mobile_hash')->nullable();
            $table->longText('photo')->nullable();
            $table->string('status')->nullable();
            $table->tinyInteger('is_verify')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aadhaar_information');
    }
};
