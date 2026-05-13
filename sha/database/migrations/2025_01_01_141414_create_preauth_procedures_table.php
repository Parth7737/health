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
        Schema::create('preauth_procedures', function (Blueprint $table) {
            $table->id();
            $table->integer('preauth_register_id');
            $table->integer('procedure_id')->nullable();
            $table->integer('speciality_id')->nullable();
            $table->string('no_of_days')->nullable();
            $table->integer('stratification_id')->nullable();
            $table->double('stratification_price',24,2)->default(0);
            $table->integer('implant_id')->nullable();
            $table->double('implant_price',24,2)->default(0);
            $table->double('procedure_price',24,2)->default(0);
            $table->double('incentive',24,2)->default(0);
            $table->integer('incentive_per')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preauth_procedures');
    }
};
