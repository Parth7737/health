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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('hospital_uuid');
            $table->bigInteger('user_id');
            $table->string('uuid');
            $table->string('order_id');
            $table->string('amount');
            $table->string('currency');
            $table->string('language');
            $table->string('billing_name');
            $table->string('billing_email');
            $table->string('billing_tel');
            $table->string('transaction_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
