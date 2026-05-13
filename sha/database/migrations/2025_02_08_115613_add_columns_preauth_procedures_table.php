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
            $table->integer('implant_qty')->nullable()->after('implant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_procedures', function (Blueprint $table) {
            $table->dropColumn(['implant_qty']);
        });
    }
};
