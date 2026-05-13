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
        Schema::create('benificiaries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('scheme_id')->nullable();
            $table->string('card_id')->nullable();
            $table->string('aabha_id')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();
            $table->text('address')->nullable();
            $table->text('care_plan')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benificiaries');
    }
};
