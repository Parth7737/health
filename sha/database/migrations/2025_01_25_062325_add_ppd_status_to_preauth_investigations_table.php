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
            $table->string('ppd_status')->nullable()->after('updated_at');
            $table->datetime('ppd_status_verify_date')->nullable()->after('ppd_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_investigations', function (Blueprint $table) {
            $table->dropColumn(['ppd_status', 'ppd_status_verify_date']);
        });
    }
};
