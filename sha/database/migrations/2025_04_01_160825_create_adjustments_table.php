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
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id')->nullable();
            $table->bigInteger('preauth_register_id')->nullable();
            $table->double('adjustment_amount',24,2)->nullable();
            $table->string('is_offline_payment')->nullable();
            $table->string('status')->nullable();
            $table->string('utr_number')->nullable();
            $table->date('transaction_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjustments');
    }
};
