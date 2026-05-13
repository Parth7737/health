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
        Schema::table('procedures', function (Blueprint $table) {
            $table->integer('scheme_type_id')->nullable()->after('name');
            $table->string('procedure_category_id')->nullable()->after('scheme_type_id');
            $table->string('procedure_type')->nullable()->after('procedure_name');
            $table->double('non_nabh_price')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procedures', function (Blueprint $table) {
            //
        });
    }
};
