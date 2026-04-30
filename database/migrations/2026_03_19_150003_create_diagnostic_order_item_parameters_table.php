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
        if (Schema::hasTable('diagnostic_order_item_parameters')) {
            return;
        }

        Schema::create('diagnostic_order_item_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnostic_order_item_id');
            $table->string('parameterable_type')->nullable();
            $table->unsignedBigInteger('parameterable_id')->nullable();
            $table->string('parameter_name');
            $table->string('unit_name')->nullable();
            $table->string('normal_range')->nullable();
            $table->string('result_value')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['parameterable_type', 'parameterable_id'], 'diag_item_param_poly_idx');
            $table->foreignId('diagnostic_order_item_id')->constrained('diagnostic_order_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_order_item_parameters');
    }
};
