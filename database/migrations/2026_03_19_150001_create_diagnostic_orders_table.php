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
        Schema::create('diagnostic_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('visitable_type');
            $table->unsignedBigInteger('visitable_id');
            $table->enum('order_type', ['pathology', 'radiology']);
            $table->string('order_no')->unique();
            $table->unsignedBigInteger('ordered_by')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['ordered', 'in_progress', 'completed', 'cancelled'])->default('ordered');
            $table->timestamps();

            $table->index(['hospital_id', 'order_type']);
            $table->index(['visitable_type', 'visitable_id']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_orders');
    }
};
