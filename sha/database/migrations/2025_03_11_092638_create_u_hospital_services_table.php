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
        Schema::create('u_hospital_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id');
            $table->string('uuid');
            $table->foreignId('service_id');
            $table->foreignId('sub_service_id');
            $table->bigInteger('action_id')->nullable();
            $table->string('service_value')->nullable();
            $table->text('text_value')->nullable();
            $table->boolean('available')->default(0);
            $table->boolean('offered')->default(0);
            $table->string('not_offered_reason')->nullable();
            $table->text('image')->nullable();
            $table->string('remark')->nullable();            
            $table->string('dec_verify_service_value')->nullable();
            $table->string('dec_verify_text_value')->nullable();
            $table->text('dec_verify_image')->nullable();
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
        Schema::dropIfExists('u_hospital_services');
    }
};
