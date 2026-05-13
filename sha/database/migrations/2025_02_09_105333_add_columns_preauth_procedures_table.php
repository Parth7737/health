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
            $table->string('preauth_claim_implant_status')->nullable()->after('preauth_claim_status');
            $table->string('preauth_claim_implant_reason')->nullable()->after('preauth_claim_implant_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->dropColumn(['preauth_claim_implant_status','preauth_claim_implant_reason']);
        });
    }
};
