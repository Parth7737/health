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
        Schema::create('u_hospital_ceos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('name');
            $table->string('designation');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('email_otp')->nullable();
            $table->string('mobile_no');
            $table->string('mobile_otp')->nullable();
            $table->string('aadhaar_no')->nullable();
            $table->tinyInteger('is_detail_added')->default(0);
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
        Schema::dropIfExists('u_hospital_ceos');
    }
};
