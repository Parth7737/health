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
        Schema::create('implants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('procedure_id')->nullable();
            $table->string('code')->nullable();
            $table->integer('no_of_multiplier')->default(1);
            $table->double('price',24,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('implants');
    }
};
