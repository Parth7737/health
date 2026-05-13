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
        Schema::create('exists_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_id')->nullable();
            $table->string('hospital_name')->nullable();
            $table->string('district')->nullable();
            $table->string('city_town')->nullable();
            $table->tinyInteger('is_added')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exists_hospitals');
    }
};
