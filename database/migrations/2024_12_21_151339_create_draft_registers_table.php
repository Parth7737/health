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
        Schema::create('draft_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('uuid')->nullable();
            $table->string('userid')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('otp')->nullable();
            $table->string('gender')->nullable();
            $table->string('hospital_type')->nullable();
            $table->string('state')->nullable();
            $table->string('avatar')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('district')->nullable();
            $table->string('register_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_registers');
    }
};
