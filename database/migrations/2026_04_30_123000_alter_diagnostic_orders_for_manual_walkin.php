<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diagnostic_orders')) {
            // Legacy MySQL + utf8mb4 can fail with long indexed varchar lengths.
            // Drop composite visitable index first, alter, then recreate.
            if ($this->indexExists('diagnostic_orders', 'diagnostic_orders_visitable_type_visitable_id_index')) {
                DB::statement('ALTER TABLE diagnostic_orders DROP INDEX diagnostic_orders_visitable_type_visitable_id_index');
            }

            // Walk-in manual orders do not always have an OPD/IPD visit context.
            DB::statement('ALTER TABLE diagnostic_orders MODIFY visitable_type VARCHAR(191) NULL');
            DB::statement('ALTER TABLE diagnostic_orders MODIFY visitable_id BIGINT UNSIGNED NULL');

            if (!$this->indexExists('diagnostic_orders', 'diagnostic_orders_visitable_type_visitable_id_index')) {
                DB::statement('ALTER TABLE diagnostic_orders ADD INDEX diagnostic_orders_visitable_type_visitable_id_index (visitable_type, visitable_id)');
            }
        }

        Schema::table('diagnostic_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('diagnostic_orders', 'doctor_staff_id')) {
                $table->unsignedBigInteger('doctor_staff_id')->nullable()->after('ordered_by');
            }
            if (!Schema::hasColumn('diagnostic_orders', 'type')) {
                $table->string('type', 30)->nullable()->after('order_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_orders', function (Blueprint $table) {
            if (Schema::hasColumn('diagnostic_orders', 'doctor_staff_id')) {
                $table->dropColumn('doctor_staff_id');
            }
            if (Schema::hasColumn('diagnostic_orders', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};