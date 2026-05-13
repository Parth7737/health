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
        Schema::table('preauth_registers', function (Blueprint $table) {
            $table->integer('is_new_born_baby')->nullable()->after('grade');
            $table->date('born_baby_dob')->nullable()->after('is_new_born_baby');
            $table->string('born_baby_name')->nullable()->after('born_baby_dob');
            $table->string('born_baby_gender')->nullable()->after('born_baby_name');
            $table->string('born_baby_birth_certificate')->nullable()->after('born_baby_gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preauth_registers', function (Blueprint $table) {
            //
        });
    }
};
