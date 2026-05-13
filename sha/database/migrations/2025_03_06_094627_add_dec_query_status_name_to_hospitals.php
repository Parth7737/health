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
            $table->string('dec_qry_id')->nullable();
            $table->string('qry_type')->nullable();
            $table->string('dec_work_id')->nullable();
            $table->date('dec_change_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['dec_id', 'qry_type', 'dec_work_id', 'dec_change_date']);
        });
    }
};
