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
            $table->dropColumn('symptoms_type_id');
            $table->json('symptoms_type_id')->nullable()->after('tpa_reference_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_patients', function (Blueprint $table) {
            //
        });
    }
};
