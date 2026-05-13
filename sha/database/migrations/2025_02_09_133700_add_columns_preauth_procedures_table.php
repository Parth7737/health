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
            $table->integer('is_implant_enhance_or_resubmission')->default(0)->after('is_enhancement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->dropColumn(['is_implant_enhance_or_resubmission']);
        });
    }
};
