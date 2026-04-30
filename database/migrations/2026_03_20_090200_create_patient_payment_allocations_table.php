<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_payment_id');
            $table->unsignedBigInteger('patient_charge_id');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->index(['patient_payment_id', 'patient_charge_id'], 'patient_pay_alloc_idx');
            $table->foreign('patient_payment_id')->references('id')->on('patient_payments')->onDelete('cascade');
            $table->foreign('patient_charge_id')->references('id')->on('patient_charges')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_payment_allocations');
    }
};
