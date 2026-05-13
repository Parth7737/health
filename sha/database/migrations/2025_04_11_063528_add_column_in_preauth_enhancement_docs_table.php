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
        Schema::table('preauth_enhancement_docs', function (Blueprint $table) {
            $table->string('medical_committee_status')->nullable()->after('is_draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_enhancement_docs', function (Blueprint $table) {
            //
        });
    }
};
