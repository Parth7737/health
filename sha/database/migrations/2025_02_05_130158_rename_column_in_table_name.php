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
        Schema::table('admission_details', function (Blueprint $table) {
            $table->renameColumn('admission_type', 'admission_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_details', function (Blueprint $table) {
            $table->renameColumn('admission_type_id', 'admission_type');
        });
    }
};
