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
            $table->text('preauth_status')->nullable()->after('incentive_per');
            $table->text('preauth_reason')->nullable()->after('preauth_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->dropColumn(['preauth_status', 'preauth_reason']);
        });
    }
};
