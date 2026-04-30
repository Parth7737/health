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
        Schema::table('diseases', function (Blueprint $table) {
            $table->unsignedBigInteger('disease_type_id')->nullable()->after('id');
            $table->foreign('disease_type_id')->references('id')->on('disease_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->dropForeign(['disease_type_id']);
            $table->dropColumn('disease_type_id');
        });
    }
};
