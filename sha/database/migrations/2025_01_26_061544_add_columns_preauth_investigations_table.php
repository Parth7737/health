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
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->integer('is_resubmission_delete')->default(0)->after('cpd_status_verify_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->dropColumn(['is_resubmission_delete']);
        });
    }
};
