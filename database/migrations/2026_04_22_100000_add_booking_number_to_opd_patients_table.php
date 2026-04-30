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
        Schema::table('opd_patients', function (Blueprint $table) {
            // Add booking_number column after case_no
            $table->string('booking_number')->nullable()->unique()->after('case_no');
            
            // Make token_no nullable (previously required at creation, now only at check-in)
            $table->unsignedBigInteger('token_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            $table->dropUnique(['booking_number']);
            $table->dropColumn('booking_number');
            
            // Revert token_no to not nullable
            $table->unsignedBigInteger('token_no')->change();
        });
    }
};
