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
        Schema::create('u_tax_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid', 255);
            $table->string('pan_no', 255)->nullable();
            $table->string('pan_name', 255)->nullable();
            $table->text('pan_certificate')->nullable();
            $table->string('tan_no', 255)->nullable();
            $table->string('tan_holder_name', 255)->nullable();
            $table->string('gst_no', 255)->nullable();
            $table->string('gst_name', 255)->nullable();
            $table->text('gst_certificate')->nullable();
            $table->string('tds_exemption', 255)->nullable();
            $table->integer('tds_exemption_id')->nullable();
            $table->double('tds_rate')->nullable();
            $table->double('after_tds_rate')->nullable();
            $table->string('tds_exemption_certificate_no', 255)->nullable();
            $table->string('tds_exemption_valid_from', 255)->nullable();
            $table->string('tds_exemption_valid_till', 255)->nullable();
            $table->double('tds_exemption_amount')->nullable();
            $table->text('tds_exemption_certificate')->nullable();
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
        Schema::dropIfExists('u_tax_details');
    }
};
