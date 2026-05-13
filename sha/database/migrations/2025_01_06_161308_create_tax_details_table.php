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
        Schema::create('tax_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('pan_no')->nullable();
            $table->string('pan_name')->nullable();
            $table->string('tan_no')->nullable();
            $table->string('tan_holder_name')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('gst_name')->nullable();
            $table->string('tds_exemption')->nullable();
            $table->integer('tds_exemption_id')->nullable();
            $table->double('tds_rate',24,2)->nullable();
            $table->double('after_tds_rate',24,2)->nullable();
            $table->string('tds_exemption_certificate_no')->nullable();
            $table->string('tds_exemption_valid_from')->nullable();
            $table->string('tds_exemption_valid_till')->nullable();
            $table->double('tds_exemption_amount',24,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_details');
    }
};
