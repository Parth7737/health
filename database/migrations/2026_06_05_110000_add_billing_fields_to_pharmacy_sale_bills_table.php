<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharmacy_sale_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('pharmacy_sale_bills', 'round_off')) {
                $table->decimal('round_off', 12, 2)->default(0.00)->after('discount_amount');
            }
            if (!Schema::hasColumn('pharmacy_sale_bills', 'payment_mode')) {
                $table->string('payment_mode', 50)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('pharmacy_sale_bills', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_sale_bills', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('pharmacy_sale_bills', 'round_off') ? 'round_off' : null,
                Schema::hasColumn('pharmacy_sale_bills', 'payment_mode') ? 'payment_mode' : null,
                Schema::hasColumn('pharmacy_sale_bills', 'payment_reference') ? 'payment_reference' : null,
            ]));
        });
    }
};
