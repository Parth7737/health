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
        Schema::create('admission_details', function (Blueprint $table) {
            $table->id();
            $table->integer('preauth_register_id');
            $table->date('admission_date')->nullable();
            $table->date('surgery_date')->nullable();
            $table->string('admission_type')->nullable();
            $table->string('legal_case')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_details');
    }
};
