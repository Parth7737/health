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
        Schema::create('u_financial_information', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('account_holder', 255)->nullable();
            $table->string('account_no', 255)->nullable();
            $table->string('ifsc_code', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_branch_name', 255)->nullable();
            $table->string('bank_address', 255)->nullable();
            $table->string('micr', 255)->nullable();
            $table->string('account_type', 255)->nullable();
            $table->string('authorised_signatory_name', 255)->nullable();
            $table->string('bank_email', 255)->nullable();
            $table->string('neft_enabled', 255)->nullable();
            $table->string('bsr_code', 255)->nullable();
            $table->string('cancelled_cheque', 255)->nullable();
            $table->string('dec_verify_status', 255)->nullable();
            $table->text('dec_verify_remark')->nullable();
            $table->bigInteger('dec_verify_id')->nullable();
            $table->string('dec_status', 255)->nullable();
            $table->text('dec_remark')->nullable();
            $table->bigInteger('dec_id')->nullable();
            $table->string('sec_status', 255)->nullable();
            $table->text('sec_remark')->nullable();
            $table->bigInteger('sec_id')->nullable();
            $table->bigInteger('main_hospitalid')->nullable();
            $table->bigInteger('old_id')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_financial_information');
    }
};
