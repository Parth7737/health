<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opd_prescription_items', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_route_id')->nullable()->after('medicine_instruction_id');
            $table->foreign('medicine_route_id')->references('id')->on('medicine_routes')->onDelete('set null');
        });

        Schema::table('ipd_prescription_items', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_route_id')->nullable()->after('medicine_instruction_id');
            $table->foreign('medicine_route_id')->references('id')->on('medicine_routes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('opd_prescription_items', function (Blueprint $table) {
            $table->dropForeign(['medicine_route_id']);
            $table->dropColumn('medicine_route_id');
        });

        Schema::table('ipd_prescription_items', function (Blueprint $table) {
            $table->dropForeign(['medicine_route_id']);
            $table->dropColumn('medicine_route_id');
        });
    }
};
