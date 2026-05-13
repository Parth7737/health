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
        Schema::create('facility_ownership_sub_types', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('facility_ownership_type_id');
            $table->string('name');
            $table->string('type')->default(0)->comment('0=type1, 1=type2, 2=type3');
            $table->bigInteger('type_id')->nullable();
            $table->bigInteger('type2_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_ownership_sub_types');
    }
};
