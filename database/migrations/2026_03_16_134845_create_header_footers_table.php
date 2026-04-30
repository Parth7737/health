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
        Schema::create('header_footers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->enum('type', ['opd_prescription', 'opd_bill', 'ipd_prescription', 'ipd_bill','pharmacy_bill','payslip','birth_record','death_record','pathology','radiology','operation_theatre','blood_bank','ambulance','discharge_summary']);
            $table->string('header_image')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_footers');
    }
};
