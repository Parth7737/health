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
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->integer('is_enhancement')->default(0)->after('preauth_reason');
            $table->integer('is_resubmission')->default(0)->after('is_enhancement');
            $table->integer('is_resubmission_delete')->default(0)->after('is_resubmission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->dropColumn(['is_enhancement', 'is_resubmission', 'is_resubmission_delete']);
        });
    }
};
