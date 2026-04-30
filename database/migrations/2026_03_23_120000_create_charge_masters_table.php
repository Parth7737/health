<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charge_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('code');
            $table->string('name');
            $table->string('category');
            $table->enum('calculation_type', ['fixed', 'daily'])->default('fixed');
            $table->enum('billing_frequency', ['one_time', 'per_day'])->default('one_time');
            $table->decimal('standard_rate', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hospital_id', 'code'], 'charge_masters_hospital_code_unique');
            $table->index(['related_type', 'related_id'], 'charge_masters_related_idx');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charge_masters');
    }
};