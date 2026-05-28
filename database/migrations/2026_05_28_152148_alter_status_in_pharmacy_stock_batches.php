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
        Schema::table('pharmacy_stock_batches', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'quarantined'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_stock_batches', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'blocked', 'out_of_stock'])->default('active')->change();
        });
    }
};
