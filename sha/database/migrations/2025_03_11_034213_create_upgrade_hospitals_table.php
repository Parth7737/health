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
        Schema::create('upgrade_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_id');
            $table->tinyInteger('establishment_details')->default(0);
            $table->tinyInteger('address')->default(0);
            $table->tinyInteger('scheme')->default(0);
            $table->tinyInteger('speciality')->default(0);
            $table->tinyInteger('services')->default(0);
            $table->tinyInteger('statutory_licences')->default(0);
            $table->tinyInteger('human_resources')->default(0);
            $table->tinyInteger('quality_accreditation')->default(0);
            $table->tinyInteger('financial_information')->default(0);
            $table->tinyInteger('tax_details')->default(0);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upgrade_hospitals');
    }
};
