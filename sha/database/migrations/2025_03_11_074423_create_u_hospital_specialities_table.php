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
        Schema::create('u_hospital_specialities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id');
            $table->string('uuid');
            $table->foreignId('speciality_id');
            $table->boolean('available')->default(0)->comment('1=>available,0=>not available');
            $table->boolean('offered')->default(0)->comment('1=>available,0=>not offered');
            $table->string('not_offered_reason')->nullable();
            $table->string('remark')->nullable();
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
            $table->bigInteger('old_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_hospital_specialities');
    }
};
