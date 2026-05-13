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
        Schema::table('investigations', function (Blueprint $table) {
            $table->bigInteger('scheme_type_id')->nullable()->after('code');
            $table->string('type')->nullable()->after('scheme_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investigations', function (Blueprint $table) {
            //
        });
    }
};
