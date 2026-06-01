<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. medicines
        Schema::table('medicines', function (Blueprint $table) {
            $table->unsignedInteger('default_pack_size')->default(1)->after('unit');
        });

        // 2. pharmacy_purchase_items
        Schema::table('pharmacy_purchase_items', function (Blueprint $table) {
            $table->unsignedInteger('pack_size_qty')->default(1)->after('pack_size');
            $table->decimal('pack_qty', 12, 2)->default(0)->after('pack_size_qty');
            $table->decimal('pack_mrp', 12, 2)->default(0)->after('pack_qty');
            $table->decimal('pack_purchase_price', 12, 2)->default(0)->after('pack_mrp');
            $table->enum('purchase_tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('pack_purchase_price');
        });

        // 3. pharmacy_grns
        Schema::table('pharmacy_grns', function (Blueprint $table) {
            $table->decimal('total_cgst', 15, 2)->default(0)->after('total_tax');
            $table->decimal('total_sgst', 15, 2)->default(0)->after('total_cgst');
            $table->decimal('total_igst', 15, 2)->default(0)->after('total_sgst');
            $table->enum('gst_type', ['local', 'interstate'])->default('local')->after('total_igst');
        });

        // 4. pharmacy_grn_items
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->unsignedInteger('pack_size')->default(1)->after('expiry_date');
            $table->decimal('pack_qty', 12, 2)->default(0)->after('pack_size');
            $table->decimal('pack_mrp', 12, 2)->default(0)->after('pack_qty');
            $table->decimal('pack_purchase_price', 12, 2)->default(0)->after('pack_mrp');
            $table->decimal('pack_sale_price', 12, 2)->default(0)->after('pack_purchase_price');
            $table->enum('purchase_tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('pack_sale_price');
            $table->enum('sale_tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('purchase_tax_type');
            $table->decimal('cgst_percent', 8, 2)->default(0)->after('tax_amount');
            $table->decimal('cgst_amount', 12, 2)->default(0)->after('cgst_percent');
            $table->decimal('sgst_percent', 8, 2)->default(0)->after('cgst_amount');
            $table->decimal('sgst_amount', 12, 2)->default(0)->after('sgst_percent');
            $table->decimal('igst_percent', 8, 2)->default(0)->after('sgst_amount');
            $table->decimal('igst_amount', 12, 2)->default(0)->after('igst_percent');
            $table->enum('gst_type', ['local', 'interstate'])->default('local')->after('igst_amount');
        });

        // 5. pharmacy_stock_batches
        Schema::table('pharmacy_stock_batches', function (Blueprint $table) {
            $table->unsignedInteger('pack_size')->default(1)->after('expiry_date');
            $table->decimal('pack_qty', 12, 2)->default(0)->after('pack_size');
            $table->decimal('pack_mrp', 12, 2)->default(0)->after('pack_qty');
            $table->decimal('pack_purchase_price', 12, 2)->default(0)->after('pack_mrp');
            $table->decimal('pack_sale_price', 12, 2)->default(0)->after('pack_purchase_price');
            $table->enum('purchase_tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('pack_sale_price');
            $table->enum('sale_tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('purchase_tax_type');
            $table->decimal('tax_percent', 8, 2)->default(0)->after('sale_tax_type');
            $table->decimal('cgst_percent', 8, 2)->default(0)->after('tax_percent');
            $table->decimal('sgst_percent', 8, 2)->default(0)->after('cgst_percent');
            $table->decimal('igst_percent', 8, 2)->default(0)->after('sgst_percent');
            $table->enum('gst_type', ['local', 'interstate'])->default('local')->after('igst_percent');
        });

        // 6. pharmacy_sale_bills
        Schema::table('pharmacy_sale_bills', function (Blueprint $table) {
            $table->decimal('total_cgst', 15, 2)->default(0)->after('tax_amount');
            $table->decimal('total_sgst', 15, 2)->default(0)->after('total_cgst');
            $table->decimal('total_igst', 15, 2)->default(0)->after('total_sgst');
        });

        // 7. pharmacy_sale_items
        Schema::table('pharmacy_sale_items', function (Blueprint $table) {
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive')->after('tax_percent');
            $table->decimal('cgst_percent', 8, 2)->default(0)->after('tax_type');
            $table->decimal('cgst_amount', 12, 2)->default(0)->after('cgst_percent');
            $table->decimal('sgst_percent', 8, 2)->default(0)->after('cgst_amount');
            $table->decimal('sgst_amount', 12, 2)->default(0)->after('sgst_percent');
            $table->decimal('igst_percent', 8, 2)->default(0)->after('sgst_amount');
            $table->decimal('igst_amount', 12, 2)->default(0)->after('igst_percent');
        });
    }

    public function down(): void
    {
        // 1. medicines
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['default_pack_size']);
        });

        // 2. pharmacy_purchase_items
        Schema::table('pharmacy_purchase_items', function (Blueprint $table) {
            $table->dropColumn(['pack_size_qty', 'pack_qty', 'pack_mrp', 'pack_purchase_price', 'purchase_tax_type']);
        });

        // 3. pharmacy_grns
        Schema::table('pharmacy_grns', function (Blueprint $table) {
            $table->dropColumn(['total_cgst', 'total_sgst', 'total_igst', 'gst_type']);
        });

        // 4. pharmacy_grn_items
        Schema::table('pharmacy_grn_items', function (Blueprint $table) {
            $table->dropColumn([
                'pack_size', 'pack_qty', 'pack_mrp', 'pack_purchase_price', 'pack_sale_price',
                'purchase_tax_type', 'sale_tax_type', 'cgst_percent', 'cgst_amount',
                'sgst_percent', 'sgst_amount', 'igst_percent', 'igst_amount', 'gst_type'
            ]);
        });

        // 5. pharmacy_stock_batches
        Schema::table('pharmacy_stock_batches', function (Blueprint $table) {
            $table->dropColumn([
                'pack_size', 'pack_qty', 'pack_mrp', 'pack_purchase_price', 'pack_sale_price',
                'purchase_tax_type', 'sale_tax_type', 'tax_percent', 'cgst_percent', 'sgst_percent', 'igst_percent', 'gst_type'
            ]);
        });

        // 6. pharmacy_sale_bills
        Schema::table('pharmacy_sale_bills', function (Blueprint $table) {
            $table->dropColumn(['total_cgst', 'total_sgst', 'total_igst']);
        });

        // 7. pharmacy_sale_items
        Schema::table('pharmacy_sale_items', function (Blueprint $table) {
            $table->dropColumn([
                'tax_type', 'cgst_percent', 'cgst_amount', 'sgst_percent', 'sgst_amount', 'igst_percent', 'igst_amount'
            ]);
        });
    }
};
