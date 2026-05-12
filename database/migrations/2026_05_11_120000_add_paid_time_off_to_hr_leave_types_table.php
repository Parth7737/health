<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_leave_types', function (Blueprint $table) {
            $table->boolean('is_paid_time_off')->default(false)->after('name');
            $table->decimal('annual_entitlement_days', 8, 2)->default(0)->after('is_paid_time_off');
        });
    }

    public function down(): void
    {
        Schema::table('hr_leave_types', function (Blueprint $table) {
            $table->dropColumn(['is_paid_time_off', 'annual_entitlement_days']);
        });
    }
};
