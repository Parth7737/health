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
        Schema::table('pharmacy_grns', function (Blueprint $table) {
            $table->renameColumn('total_value', 'total_amount');
            $table->decimal('total_tax', 15, 2)->after('total_amount')->default(0);
            $table->decimal('taxable_amount', 15, 2)->after('total_tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_grns', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'total_value');
            $table->dropColumn(['total_tax', 'taxable_amount']);
        });
    }
};
