<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('visitable_type')->nullable();
            $table->unsignedBigInteger('visitable_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_mode')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'patient_id']);
            $table->index(['visitable_type', 'visitable_id'], 'patient_payments_visitable_idx');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_payments');
    }
};
