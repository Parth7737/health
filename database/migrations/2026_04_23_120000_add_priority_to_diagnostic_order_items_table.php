<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('diagnostic_order_items', 'priority')) {
                $table->enum('priority', ['Routine', 'Urgent', 'STAT'])
                    ->default('Routine')
                    ->after('category_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('diagnostic_order_items', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};