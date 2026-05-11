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
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'employment_category')) {
                $table->string('employment_category')->nullable()->after('work_experience');
            }

            if (!Schema::hasColumn('staff', 'basic_pay')) {
                $table->decimal('basic_pay', 12, 2)->nullable()->after('employment_category');
            }

            if (!Schema::hasColumn('staff', 'shift_timing')) {
                $table->string('shift_timing')->nullable()->after('basic_pay');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasColumn('staff', 'shift_timing')) {
                $table->dropColumn('shift_timing');
            }
            if (Schema::hasColumn('staff', 'basic_pay')) {
                $table->dropColumn('basic_pay');
            }
            if (Schema::hasColumn('staff', 'employment_category')) {
                $table->dropColumn('employment_category');
            }
        });
    }
};
