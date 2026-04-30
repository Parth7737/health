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
            if (!Schema::hasColumn('diagnostic_order_items', 'sample_collected_at')) {
                $table->timestamp('sample_collected_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'sample_collected_by')) {
                $table->unsignedBigInteger('sample_collected_by')->nullable()->after('sample_collected_at');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'pathology_status_id')) {
                $table->unsignedBigInteger('pathology_status_id')->nullable()->after('sample_collected_by');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'report_impression')) {
                $table->text('report_impression')->nullable()->after('report_summary');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('standard_charge');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'net_amount')) {
                $table->decimal('net_amount', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('net_amount');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('paid_amount');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'payment_mode')) {
                $table->string('payment_mode')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'billed_at')) {
                $table->timestamp('billed_at')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('diagnostic_order_items', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('billed_at');
            }
        });

        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (!$this->indexExists('diagnostic_order_items', 'diag_item_dept_payment_idx')) {
                $table->index(['department', 'payment_status'], 'diag_item_dept_payment_idx');
            }
            if (!$this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_pathology_status_id_foreign')) {
                $table->foreign('pathology_status_id')->references('id')->on('pathology_statuses')->onDelete('set null');
            }
            if (!$this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_sample_collected_by_foreign')) {
                $table->foreign('sample_collected_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('diagnostic_order_items', 'pathology_status_id')) {
                if ($this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_pathology_status_id_foreign')) {
                    $table->dropForeign(['pathology_status_id']);
                }
            }
            if (Schema::hasColumn('diagnostic_order_items', 'sample_collected_by')) {
                if ($this->foreignKeyExists('diagnostic_order_items', 'diagnostic_order_items_sample_collected_by_foreign')) {
                    $table->dropForeign(['sample_collected_by']);
                }
            }
            if ($this->indexExists('diagnostic_order_items', 'diag_item_dept_payment_idx')) {
                $table->dropIndex('diag_item_dept_payment_idx');
            }

            $dropColumns = [
                'sample_collected_at',
                'sample_collected_by',
                'pathology_status_id',
                'report_impression',
                'discount_amount',
                'tax_amount',
                'net_amount',
                'paid_amount',
                'payment_status',
                'payment_mode',
                'payment_reference',
                'billed_at',
                'paid_at',
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
