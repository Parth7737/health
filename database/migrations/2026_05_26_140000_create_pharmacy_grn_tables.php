<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand PO status to support GRN fulfilment tracking
        DB::statement("ALTER TABLE pharmacy_purchase_bills MODIFY COLUMN status ENUM('pending','approved','rejected','partially_received','received') DEFAULT 'pending'");

        Schema::create('pharmacy_grns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('grn_no');
            $table->unsignedBigInteger('purchase_bill_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('temperature_status')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_value', 12, 2)->default(0);
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'grn_no']);
            $table->index(['hospital_id', 'purchase_bill_id']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('purchase_bill_id')->references('id')->on('pharmacy_purchase_bills')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('pharmacy_suppliers')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('pharmacy_grn_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('purchase_item_id');
            $table->unsignedBigInteger('medicine_id');
            $table->string('batch_no');
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity_ordered', 12, 2)->default(0);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->decimal('quantity_rejected', 12, 2)->default(0);
            $table->decimal('quantity_accepted', 12, 2)->default(0);
            $table->string('rejection_reason')->nullable();
            $table->decimal('unit_purchase_price', 12, 2)->default(0);
            $table->decimal('unit_sale_price', 12, 2)->default(0);
            $table->decimal('unit_mrp', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('grn_id')->references('id')->on('pharmacy_grns')->onDelete('cascade');
            $table->foreign('purchase_item_id')->references('id')->on('pharmacy_purchase_items')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_grn_items');
        Schema::dropIfExists('pharmacy_grns');

        DB::statement("ALTER TABLE pharmacy_purchase_bills MODIFY COLUMN status ENUM('pending','approved','rejected') DEFAULT 'pending'");
    }
};
