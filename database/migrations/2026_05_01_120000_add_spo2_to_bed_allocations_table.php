<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (! Schema::hasColumn('bed_allocations', 'spo2')) {
                $table->string('spo2', 50)->nullable()->after('diastolic_bp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (Schema::hasColumn('bed_allocations', 'spo2')) {
                $table->dropColumn('spo2');
            }
        });
    }
};
