<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_attendance_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable()->index();
            $table->unsignedBigInteger('attendance_record_id')->nullable()->index();
            $table->unsignedBigInteger('staff_id')->nullable()->index();
            $table->date('attendance_date')->nullable()->index();
            $table->unsignedBigInteger('edited_by_user_id')->nullable()->index();
            $table->text('change_summary')->nullable();
            $table->longText('old_data')->nullable();
            $table->longText('new_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_edit_logs');
    }
};
