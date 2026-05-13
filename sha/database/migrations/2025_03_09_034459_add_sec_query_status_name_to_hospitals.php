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
            $table->string('sec_qry_id')->nullable();
            $table->string('sec_type')->nullable();
            $table->string('sec_work_id')->nullable();
            $table->date('sec_change_date')->nullable();
            $table->integer('is_empanelled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['sec_qry_id', 'sec_type', 'sec_work_id', 'sec_change_date']);
        });
    }
};
