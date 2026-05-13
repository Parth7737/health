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
        Schema::create('preauth_remarks', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('added_by')->nullable(); // ID of the user who added the remark
            $table->bigInteger('content_id')->nullable();
            $table->morphs('remarkable'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preauth_remarks');
    }
};
