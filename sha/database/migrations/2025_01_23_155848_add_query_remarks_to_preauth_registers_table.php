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
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->string('preauth_query_supporting_doc')->nullable();
            $table->string('preauth_query_add_doc')->nullable();
            $table->string('query_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->dropColumn(['preauth_query_supporting_doc', 'preauth_query_add_doc','query_remarks']);
        });
    }
};
