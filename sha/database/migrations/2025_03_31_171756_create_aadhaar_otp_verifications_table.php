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
        Schema::create('aadhaar_otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('aadhaar_no')->nullable();
            $table->string('aadhaar_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->tinyInteger('is_verify')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aadhaar_otp_verifications');
    }
};
