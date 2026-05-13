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
            $table->bigInteger('assigned_to_ppd')->nullable();
            $table->bigInteger('assigned_to_cex')->nullable();
            $table->bigInteger('assigned_to_cpd')->nullable();
            $table->bigInteger('assigned_to_aco')->nullable();
            $table->bigInteger('assigned_to_sha')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            //
        });
    }
};
