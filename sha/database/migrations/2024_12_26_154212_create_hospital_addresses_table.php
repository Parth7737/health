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
        Schema::create('hospital_addresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
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
        Schema::dropIfExists('hospital_addresses');
    }
};
