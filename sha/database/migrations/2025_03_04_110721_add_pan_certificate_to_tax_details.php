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
        Schema::table('tax_details', function (Blueprint $table) {
           $table->text('pan_certificate')->nullable()->after('pan_name');
           $table->text('gst_certificate')->nullable()->after('gst_name');
           $table->text('tds_exemption_certificate')->nullable()->after('tds_exemption_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_details', function (Blueprint $table) {
            $table->dropColumn(['pan_certificate', 'gst_certificate', 'tds_exemption_certificate']);
        });
    }
};
