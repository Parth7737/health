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
        Schema::create('financial_information', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('account_holder')->nullable();
            $table->string('account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('micr')->nullable();
            $table->string('account_type')->nullable();
            $table->string('authorised_signatory_name')->nullable();
            $table->string('bank_email')->nullable();
            $table->string('neft_enabled')->nullable();
            $table->string('bsr_code')->nullable();
            $table->string('cancelled_cheque')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_information');
    }
};
