<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('diagnostic_order_items', 'critical_doctor_called_at')) {
                $table->timestamp('critical_doctor_called_at')->nullable()->after('reported_at');
            }

            if (!Schema::hasColumn('diagnostic_order_items', 'critical_acknowledged_at')) {
                $table->timestamp('critical_acknowledged_at')->nullable()->after('critical_doctor_called_at');
            }

            if (!Schema::hasColumn('diagnostic_order_items', 'critical_acknowledged_by')) {
                $table->unsignedBigInteger('critical_acknowledged_by')->nullable()->after('critical_acknowledged_at');
            }
        });

        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (!$this->indexExists('diagnostic_order_items', 'diag_item_critical_ack_idx')) {
                $table->index(['critical_acknowledged_at'], 'diag_item_critical_ack_idx');
            }

            if (!$this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_critical_acknowledged_by_foreign')) {
                $table->foreign('critical_acknowledged_by', 'diagnostic_order_items_critical_acknowledged_by_foreign')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('diagnostic_order_items', 'critical_acknowledged_by')) {
                if ($this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_critical_acknowledged_by_foreign')) {
                    $table->dropForeign('diagnostic_order_items_critical_acknowledged_by_foreign');
                }
            }

            if ($this->indexExists('diagnostic_order_items', 'diag_item_critical_ack_idx')) {
                $table->dropIndex('diag_item_critical_ack_idx');
            }

            $dropColumns = [
                'critical_doctor_called_at',
                'critical_acknowledged_at',
                'critical_acknowledged_by',
            ];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('diagnostic_order_items', $column)) {
                    $table->dropColumn($column);
                }
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

    protected function foreignKeyExists(string $table, string $constraintName): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
