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
        Schema::create('sub_service_actions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sub_service_id');
            $table->enum('type', ['radio', 'text', 'checkbox']);
            $table->string('label');
            $table->string('value')->nullable();
            $table->tinyInteger('is_text_input')->default(0)->comment('0=No, 1=Yes');
            $table->tinyInteger('is_image')->default(0)->comment('0=No, 1=Yes');
            $table->string('sublabel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_service_actions');
    }
};
