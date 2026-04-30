<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('diagnostic_order_item_parameters')) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM diagnostic_order_item_parameters"))
            ->pluck('Key_name')
            ->all();

        if (!in_array('diag_item_param_poly_idx', $existingIndexes, true)) {
            DB::statement('CREATE INDEX diag_item_param_poly_idx ON diagnostic_order_item_parameters (parameterable_type, parameterable_id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('diagnostic_order_item_parameters')) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM diagnostic_order_item_parameters"))
            ->pluck('Key_name')
            ->all();

        if (in_array('diag_item_param_poly_idx', $existingIndexes, true)) {
            DB::statement('DROP INDEX diag_item_param_poly_idx ON diagnostic_order_item_parameters');
        }
    }
};
