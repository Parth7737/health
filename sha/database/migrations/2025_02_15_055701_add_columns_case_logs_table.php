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
        Schema::table('case_logs', function (Blueprint $table) {
            $table->string('status')->nullable()->after('preauth_register_id');
            $table->string('amount')->nullable()->after('status');
            $table->integer('role_id')->nullable()->after('amount');
            $table->string('stage')->nullable()->after('role_id');
            $table->longText('procedures')->nullable()->after('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_logs', function (Blueprint $table) {
            //
        });
    }
};
