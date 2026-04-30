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
        Schema::create('diagnostic_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnostic_order_id');
            $table->enum('department', ['pathology', 'radiology']);
            $table->string('testable_type');
            $table->unsignedBigInteger('testable_id');
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->string('category_name')->nullable();
            $table->text('sample_type')->nullable();
            $table->string('method')->nullable();
            $table->text('expected_report_days')->nullable();
            $table->decimal('standard_charge', 10, 2)->default(0);
            $table->enum('status', ['ordered', 'sample_collected', 'in_progress', 'completed', 'cancelled'])->default('ordered');
            $table->longText('report_text')->nullable();
            $table->text('report_summary')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();

            $table->index(['department', 'status']);
            $table->index(['testable_type', 'testable_id']);
            $table->foreign('diagnostic_order_id')->references('id')->on('diagnostic_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_order_items');
    }
};
