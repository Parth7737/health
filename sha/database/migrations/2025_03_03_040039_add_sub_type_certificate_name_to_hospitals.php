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
        Schema::table('hospitals', function (Blueprint $table) {            
            $table->string('sub_type_certificate_name')->nullable()->after('facility_ownership_type');
            $table->text('sub_type_certificate')->nullable()->after('sub_type_certificate_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['sub_type_certificate_name', 'sub_type_certificate']);
        });
    }
};
