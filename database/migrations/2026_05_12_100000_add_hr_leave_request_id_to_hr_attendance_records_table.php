<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hr_attendance_records')) {
            return;
        }
        if (Schema::hasColumn('hr_attendance_records', 'hr_leave_request_id')) {
            return;
        }

        Schema::table('hr_attendance_records', function (Blueprint $table) {
            $table->foreignId('hr_leave_request_id')->nullable()->after('staff_id')->constrained('hr_leave_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hr_attendance_records')) {
            return;
        }
        if (!Schema::hasColumn('hr_attendance_records', 'hr_leave_request_id')) {
            return;
        }

        Schema::table('hr_attendance_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hr_leave_request_id');
        });
    }
};
