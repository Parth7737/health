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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->string('staff_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->references('id')->on('roles')->onDelete('set null');
            $table->foreignId('hr_specialist_id')->nullable()->references('id')->on('hr_specialists')->onDelete('set null');
            $table->foreignId('hr_designation_id')->nullable()->references('id')->on('hr_designations')->onDelete('set null');
            $table->foreignId('hr_department_id')->nullable()->references('id')->on('hr_departments')->onDelete('set null');
            $table->foreignId('hod_id')->nullable()->references('id')->on('staff')->onDelete('set null');
            $table->foreignId('linemanager_id')->nullable()->references('id')->on('staff')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced','Not Specified'])->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->integer('phone')->nullable();
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('work_experience')->nullable();
            $table->text('specialization')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->integer('pto')->default(0);
            $table->integer('vacation')->default(0);
            $table->integer('fmla')->default(0);
            $table->integer('education_leave')->default(0);
            $table->integer('test_leave')->default(0);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_account_holder_name')->nullable();
            $table->json('work_timings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
