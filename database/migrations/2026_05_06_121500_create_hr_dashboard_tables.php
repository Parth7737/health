<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('attendance_date');
            $table->string('shift_name')->nullable();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Leave', 'Holiday'])->default('Present');
            $table->unsignedTinyInteger('late_count')->default(0);
            $table->enum('day_type', ['Full Day', 'Half Day'])->nullable();
            $table->boolean('is_miss_punch')->default(false);
            $table->boolean('is_overtime')->default(false);
            $table->string('combined_status', 60)->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->unique(['hospital_id', 'staff_id', 'attendance_date'], 'hr_attendance_unique');
            $table->index(['hospital_id', 'attendance_date']);
        });

        Schema::create('hr_payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('payroll_month');
            $table->decimal('basic_pay', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->enum('status', ['Pending', 'Generated', 'Paid'])->default('Pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['hospital_id', 'staff_id', 'payroll_month'], 'hr_payroll_unique');
            $table->index(['hospital_id', 'payroll_month']);
        });

        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('request_no')->unique();
            $table->foreignId('hr_leave_type_id')->nullable()->constrained('hr_leave_types')->nullOnDelete();
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('total_days', 5, 2)->default(0);
            $table->text('reason')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['hospital_id', 'status']);
            $table->index(['hospital_id', 'from_date', 'to_date']);
        });

        Schema::create('hr_recruitment_vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->string('title');
            $table->unsignedInteger('required_positions')->default(1);
            $table->unsignedInteger('applicants')->default(0);
            $table->unsignedInteger('shortlisted')->default(0);
            $table->string('status', 40)->default('Open');
            $table->date('last_date')->nullable();
            $table->timestamps();
            $table->index(['hospital_id', 'status']);
        });

        Schema::create('hr_training_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->string('title');
            $table->string('category')->nullable();
            $table->date('schedule_date');
            $table->string('trainer_name')->nullable();
            $table->unsignedInteger('participants')->default(0);
            $table->enum('status', ['Scheduled', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->timestamps();
            $table->index(['hospital_id', 'schedule_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_training_programs');
        Schema::dropIfExists('hr_recruitment_vacancies');
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_payroll_records');
        Schema::dropIfExists('hr_attendance_records');
    }
};
