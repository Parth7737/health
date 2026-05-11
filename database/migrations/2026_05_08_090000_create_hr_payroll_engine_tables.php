<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->unsignedInteger('standard_working_days')->default(30);
            $table->decimal('leave_deduction_per_day', 12, 2)->default(0);
            $table->timestamps();
            $table->unique('hospital_id');
        });

        Schema::create('hr_payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->string('name');
            $table->enum('component_type', ['Allowance', 'Deduction']);
            $table->enum('value_type', ['Fixed', 'Percentage']);
            $table->decimal('value', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['hospital_id', 'component_type', 'is_active'], 'hr_payroll_comp_type_idx');
        });

        Schema::create('hr_payroll_record_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('hr_payroll_record_id')->constrained('hr_payroll_records')->onDelete('cascade');
            $table->string('label');
            $table->enum('item_type', ['Allowance', 'Deduction']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['hospital_id', 'hr_payroll_record_id'], 'hr_payroll_items_record_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_record_items');
        Schema::dropIfExists('hr_payroll_components');
        Schema::dropIfExists('hr_payroll_settings');
    }
};
