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
        Schema::create('u_hospital_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id');
            $table->string('uuid');
            $table->string('address');
            $table->string('pincode');
            $table->string('block');
            $table->string('village');
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('landmark')->nullable();
            $table->string('telephone')->nullable();
            $table->string('std_code')->nullable();
            $table->string('mobile_no');
            $table->string('otp')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('police_station');
            $table->string('locality');
            $table->string('latitude');
            $table->string('longitude');
            $table->boolean('is_added')->default(false);
            $table->boolean('is_approve')->default(false);
            $table->string('dec_verify_status')->nullable();
            $table->text('dec_verify_remark')->nullable();
            $table->bigInteger('dec_verify_id')->nullable();
            $table->string('dec_status')->nullable();
            $table->text('dec_remark')->nullable();
            $table->bigInteger('dec_id')->nullable();
            $table->string('sec_status')->nullable();
            $table->text('sec_remark')->nullable();
            $table->bigInteger('sec_id')->nullable();
            $table->bigInteger('main_hospitalid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_hospital_addresses');
    }
};
