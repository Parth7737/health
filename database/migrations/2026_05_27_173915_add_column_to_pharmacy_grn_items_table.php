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
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->decimal('taxable_amount', 15, 2)->after('tax_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->dropColumn('taxable_amount');
        });
    }
};
