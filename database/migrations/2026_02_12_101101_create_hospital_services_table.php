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
        Schema::create('hospital_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->bigInteger('service_id');
            $table->bigInteger('sub_service_id');
            $table->bigInteger('action_id')->nullable();
            $table->string('service_value')->nullable();
            $table->string('text_value')->nullable();
            $table->text('image')->nullable();
            $table->string('remark')->nullable();
            $table->string('admin_verify_status')->nullable();
            $table->text('admin_verify_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_services');
    }
};
