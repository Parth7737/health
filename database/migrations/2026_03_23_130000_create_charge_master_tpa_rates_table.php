<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charge_master_tpa_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('charge_master_id');
            $table->unsignedBigInteger('tpa_id');
            $table->decimal('rate', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['charge_master_id', 'tpa_id'], 'charge_master_tpa_unique');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('charge_master_id')->references('id')->on('charge_masters')->onDelete('cascade');
            $table->foreign('tpa_id')->references('id')->on('tpas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charge_master_tpa_rates');
    }
};
