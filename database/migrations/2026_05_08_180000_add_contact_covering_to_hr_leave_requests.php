<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->string('contact_during_leave', 20)->nullable()->after('reason');
            $table->string('covering_staff', 150)->nullable()->after('contact_during_leave');
        });
    }

    public function down(): void
    {
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->dropColumn(['contact_during_leave', 'covering_staff']);
        });
    }
};
