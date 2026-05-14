<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (! Schema::hasColumn('bed_allocations', 'scheme_type_id')) {
                $table->foreignId('scheme_type_id')->nullable()->after('tpa_reference_no')->constrained('scheme_types')->nullOnDelete();
            }
            if (! Schema::hasColumn('bed_allocations', 'scheme_beneficiary_card_id')) {
                $table->string('scheme_beneficiary_card_id', 191)->nullable()->after('scheme_type_id');
            }
            if (! Schema::hasColumn('bed_allocations', 'scheme_kyc_type')) {
                $table->string('scheme_kyc_type', 32)->nullable()->after('scheme_beneficiary_card_id');
            }
            if (! Schema::hasColumn('bed_allocations', 'scheme_beneficiary_verified_at')) {
                $table->timestamp('scheme_beneficiary_verified_at')->nullable()->after('scheme_kyc_type');
            }
            if (! Schema::hasColumn('bed_allocations', 'scheme_is_newborn')) {
                $table->boolean('scheme_is_newborn')->default(false)->after('scheme_beneficiary_verified_at');
            }
            if (! Schema::hasColumn('bed_allocations', 'payment_mode_label')) {
                $table->string('payment_mode_label', 120)->nullable()->after('scheme_is_newborn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bed_allocations', function (Blueprint $table) {
            if (Schema::hasColumn('bed_allocations', 'payment_mode_label')) {
                $table->dropColumn('payment_mode_label');
            }
            if (Schema::hasColumn('bed_allocations', 'scheme_is_newborn')) {
                $table->dropColumn('scheme_is_newborn');
            }
            if (Schema::hasColumn('bed_allocations', 'scheme_beneficiary_verified_at')) {
                $table->dropColumn('scheme_beneficiary_verified_at');
            }
            if (Schema::hasColumn('bed_allocations', 'scheme_kyc_type')) {
                $table->dropColumn('scheme_kyc_type');
            }
            if (Schema::hasColumn('bed_allocations', 'scheme_beneficiary_card_id')) {
                $table->dropColumn('scheme_beneficiary_card_id');
            }
            if (Schema::hasColumn('bed_allocations', 'scheme_type_id')) {
                $table->dropConstrainedForeignId('scheme_type_id');
            }
        });
    }
};
