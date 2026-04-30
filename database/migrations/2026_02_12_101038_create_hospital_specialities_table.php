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
        Schema::create('hospital_specialities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->bigInteger('speciality_id');
            $table->tinyInteger('available')->default('0')->comment('1=>available,0=>notAvailable');
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
        Schema::dropIfExists('hospital_specialities');
    }
};
