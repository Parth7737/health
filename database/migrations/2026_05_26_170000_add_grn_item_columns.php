<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->decimal('quantity_free', 12, 2)->default(0)->after('quantity_received');
            $table->decimal('tax_percent', 8, 2)->default(0)->after('unit_mrp');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percent');
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_free', 'tax_percent', 'tax_amount']);
        });
    }
};
