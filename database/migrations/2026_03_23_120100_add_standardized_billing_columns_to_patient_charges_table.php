<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_charges', 'charge_master_id')) {
                $table->unsignedBigInteger('charge_master_id')->nullable()->after('module');
            }
            if (!Schema::hasColumn('patient_charges', 'charge_code')) {
                $table->string('charge_code')->nullable()->after('charge_master_id');
            }
            if (!Schema::hasColumn('patient_charges', 'charge_category')) {
                $table->string('charge_category')->nullable()->after('charge_code');
            }
            if (!Schema::hasColumn('patient_charges', 'calculation_type')) {
                $table->enum('calculation_type', ['fixed', 'daily'])->default('fixed')->after('charge_category');
            }
            if (!Schema::hasColumn('patient_charges', 'billing_frequency')) {
                $table->enum('billing_frequency', ['one_time', 'per_day'])->default('one_time')->after('calculation_type');
            }
            if (!Schema::hasColumn('patient_charges', 'quantity')) {
                $table->decimal('quantity', 10, 2)->default(1)->after('billing_frequency');
            }
            if (!Schema::hasColumn('patient_charges', 'unit_rate')) {
                $table->decimal('unit_rate', 10, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('patient_charges', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_rate');
            }
            if (!Schema::hasColumn('patient_charges', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('patient_charges', 'net_amount')) {
                $table->decimal('net_amount', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('patient_charges', 'payer_type')) {
                $table->enum('payer_type', ['self', 'tpa'])->default('self')->after('net_amount');
            }
            if (!Schema::hasColumn('patient_charges', 'tpa_id')) {
                $table->unsignedBigInteger('tpa_id')->nullable()->after('payer_type');
            }
        });

        Schema::table('patient_charges', function (Blueprint $table) {
            if (Schema::hasColumn('patient_charges', 'charge_master_id')) {
                $table->index('charge_master_id', 'patient_charges_charge_master_idx');
                $table->foreign('charge_master_id')->references('id')->on('charge_masters')->onDelete('set null');
            }
            if (Schema::hasColumn('patient_charges', 'tpa_id')) {
                $table->index('tpa_id', 'patient_charges_tpa_idx');
                $table->foreign('tpa_id')->references('id')->on('tpas')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patient_charges', function (Blueprint $table) {
            if (Schema::hasColumn('patient_charges', 'charge_master_id')) {
                $table->dropForeign(['charge_master_id']);
                $table->dropIndex('patient_charges_charge_master_idx');
            }
            if (Schema::hasColumn('patient_charges', 'tpa_id')) {
                $table->dropForeign(['tpa_id']);
                $table->dropIndex('patient_charges_tpa_idx');
            }

            $dropColumns = [
                'charge_master_id',
                'charge_code',
                'charge_category',
                'calculation_type',
                'billing_frequency',
                'quantity',
                'unit_rate',
                'discount_amount',
                'tax_amount',
                'net_amount',
                'payer_type',
                'tpa_id',
            ];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('patient_charges', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};