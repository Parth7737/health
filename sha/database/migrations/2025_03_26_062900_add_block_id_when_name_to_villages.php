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
        Schema::table('villages', function (Blueprint $table) {
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('state_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn(['block_id', 'state_id']);
        });
    }
};
