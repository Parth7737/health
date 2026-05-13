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
        Schema::create('facility_details', function (Blueprint $table) {
            $table->id();
            $table->string('facility_id', 191)->unique();
            $table->string('facility_name');
            $table->string('state');
            $table->string('district');
            $table->string('sub_district');
            $table->string('facility_ownership');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_details');
    }
};
