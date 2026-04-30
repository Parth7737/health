<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('gstin', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->index('hospital_id');
        });

        Schema::table('pharmacy_purchase_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('supplier_invoice_no');
            $table->string('discount_type', 10)->default('fixed')->after('discount_amount'); // 'percent' or 'fixed'
            $table->foreign('supplier_id')->references('id')->on('pharmacy_suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_purchase_bills', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'discount_type']);
        });

        Schema::dropIfExists('pharmacy_suppliers');
    }
};
