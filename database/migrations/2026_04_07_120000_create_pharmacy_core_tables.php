<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_purchase_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('bill_no');
            $table->date('bill_date');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_invoice_no')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('round_off', 12, 2)->default(0);
            $table->decimal('net_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'bill_no'], 'pharmacy_purchase_bills_hospital_bill_no_unique');
            $table->index(['hospital_id', 'bill_date']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('pharmacy_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_bill_id');
            $table->unsignedBigInteger('medicine_id');
            $table->string('batch_no');
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('pack_size')->nullable();
            $table->decimal('unit_purchase_price', 12, 2)->default(0);
            $table->decimal('unit_sale_price', 12, 2)->default(0);
            $table->decimal('unit_mrp', 12, 2)->default(0);
            $table->decimal('quantity_purchased', 12, 2)->default(0);
            $table->decimal('quantity_free', 12, 2)->default(0);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->decimal('total_quantity', 12, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('line_subtotal', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['medicine_id', 'batch_no']);
            $table->foreign('purchase_bill_id')->references('id')->on('pharmacy_purchase_bills')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });

        Schema::create('pharmacy_stock_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('purchase_item_id')->nullable();
            $table->string('batch_no');
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('unit_purchase_price', 12, 2)->default(0);
            $table->decimal('unit_sale_price', 12, 2)->default(0);
            $table->decimal('unit_mrp', 12, 2)->default(0);
            $table->decimal('available_qty', 12, 2)->default(0);
            $table->decimal('reserved_qty', 12, 2)->default(0);
            $table->decimal('damaged_qty', 12, 2)->default(0);
            $table->decimal('expired_qty', 12, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'blocked', 'out_of_stock'])->default('active');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('last_expiry_processed_at')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'medicine_id', 'status']);
            $table->index(['expiry_date', 'status']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('purchase_item_id')->references('id')->on('pharmacy_purchase_items')->onDelete('set null');
        });

        Schema::create('pharmacy_sale_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('visitable_type')->nullable();
            $table->unsignedBigInteger('visitable_id')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('opd_prescription_id')->nullable();
            $table->unsignedBigInteger('ipd_prescription_id')->nullable();
            $table->string('bill_no');
            $table->date('bill_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('net_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->boolean('is_from_prescription')->default(false);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'bill_no'], 'pharmacy_sale_bills_hospital_bill_no_unique');
            $table->index(['hospital_id', 'bill_date']);
            $table->index(['visitable_type', 'visitable_id'], 'pharmacy_sale_bills_visitable_idx');
            $table->index(['source_type', 'source_id'], 'pharmacy_sale_bills_source_idx');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('opd_prescription_id')->references('id')->on('opd_prescriptions')->onDelete('set null');
            $table->foreign('ipd_prescription_id')->references('id')->on('ipd_prescriptions')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('pharmacy_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_bill_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('stock_batch_id')->nullable();
            $table->string('batch_no');
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('unit_mrp', 12, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('line_subtotal', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->boolean('is_substituted')->default(false);
            $table->text('substitution_note')->nullable();
            $table->timestamps();

            $table->index(['medicine_id', 'batch_no']);
            $table->foreign('sale_bill_id')->references('id')->on('pharmacy_sale_bills')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('stock_batch_id')->references('id')->on('pharmacy_stock_batches')->onDelete('set null');
        });

        Schema::create('pharmacy_stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('stock_batch_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('entry_type', ['in', 'out', 'adjustment_expiry', 'adjustment_damage', 'return_in', 'return_out']);
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->decimal('unit_purchase_price', 12, 2)->default(0);
            $table->decimal('unit_sale_price', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamp('entry_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'medicine_id', 'entry_at']);
            $table->index(['reference_type', 'reference_id'], 'pharmacy_stock_ledgers_reference_idx');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('stock_batch_id')->references('id')->on('pharmacy_stock_batches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_stock_ledgers');
        Schema::dropIfExists('pharmacy_sale_items');
        Schema::dropIfExists('pharmacy_sale_bills');
        Schema::dropIfExists('pharmacy_stock_batches');
        Schema::dropIfExists('pharmacy_purchase_items');
        Schema::dropIfExists('pharmacy_purchase_bills');
    }
};
