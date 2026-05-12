<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * If an older copy of 2026_05_12_100000 already created treatment_plan_* tables,
 * drop them and create SHA-named tables (packages, procedures, …).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packages')) {
            $this->dropLegacyPrefixedTablesIfAny();

            return;
        }

        if (! Schema::hasTable('treatment_plan_packages')) {
            return;
        }

        $this->dropLegacyPrefixedTables();

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 64);
            $table->timestamps();
        });

        Schema::create('investigations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 64);
            $table->timestamps();
        });

        Schema::create('procedure_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 64);
            $table->timestamps();
        });

        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('procedure_category_id')->nullable();
            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->string('name');
            $table->string('procedure_code', 128)->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->string('status', 32)->default('active');
            $table->timestamps();
        });

        Schema::table('procedures', function (Blueprint $table) {
            $table->foreign('package_id', 'fk_proc_pkg')->references('id')->on('packages')->nullOnDelete();
            $table->foreign('procedure_category_id', 'fk_proc_pcat')->references('id')->on('procedure_categories')->nullOnDelete();
            $table->foreign('speciality_id', 'fk_proc_spec')->references('id')->on('specialities')->nullOnDelete();
        });

        Schema::create('implants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedure_id')->nullable();
            $table->string('name');
            $table->string('code', 64)->nullable();
            $table->unsignedInteger('no_of_multiplier')->default(1);
            $table->decimal('price', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('implants', function (Blueprint $table) {
            $table->foreign('procedure_id', 'fk_imp_proc')->references('id')->on('procedures')->nullOnDelete();
        });

        Schema::create('stratification_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('stratifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stratification_category_id');
            $table->unsignedBigInteger('procedure_id')->nullable();
            $table->string('name');
            $table->string('code', 64)->nullable();
            $table->string('code2', 64)->nullable();
            $table->string('rule')->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('stratifications', function (Blueprint $table) {
            $table->foreign('stratification_category_id', 'fk_strat_cat')->references('id')->on('stratification_categories')->cascadeOnDelete();
            $table->foreign('procedure_id', 'fk_strat_proc')->references('id')->on('procedures')->nullOnDelete();
        });

        Schema::create('followup_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedure_id');
            $table->unsignedBigInteger('follow_up_id');
            $table->timestamps();
        });

        Schema::table('followup_procedures', function (Blueprint $table) {
            $table->foreign('procedure_id', 'fk_fu_proc')->references('id')->on('procedures')->cascadeOnDelete();
            $table->foreign('follow_up_id', 'fk_fu_follow')->references('id')->on('procedures')->cascadeOnDelete();
        });

        Schema::create('add_on_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedure_id');
            $table->unsignedBigInteger('add_on_id');
            $table->timestamps();
        });

        Schema::table('add_on_procedures', function (Blueprint $table) {
            $table->foreign('procedure_id', 'fk_ao_proc')->references('id')->on('procedures')->cascadeOnDelete();
            $table->foreign('add_on_id', 'fk_ao_addon')->references('id')->on('procedures')->cascadeOnDelete();
        });

        Schema::create('non_add_on_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedure_id');
            $table->unsignedBigInteger('non_add_on_id');
            $table->timestamps();
        });

        Schema::table('non_add_on_procedures', function (Blueprint $table) {
            $table->foreign('procedure_id', 'fk_na_proc')->references('id')->on('procedures')->cascadeOnDelete();
            $table->foreign('non_add_on_id', 'fk_na_other')->references('id')->on('procedures')->cascadeOnDelete();
        });

        Schema::create('add_on_specialities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('add_on_id');
            $table->unsignedBigInteger('speciality_id');
            $table->timestamps();
            $table->unique(['add_on_id', 'speciality_id'], 'add_on_spec_unique');
        });

        Schema::table('add_on_specialities', function (Blueprint $table) {
            $table->foreign('add_on_id', 'fk_as_addon')->references('id')->on('procedures')->cascadeOnDelete();
            $table->foreign('speciality_id', 'fk_as_spec')->references('id')->on('specialities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // No safe rollback after data moved; legacy names are deprecated.
    }

    private function dropLegacyPrefixedTablesIfAny(): void
    {
        if (! Schema::hasTable('treatment_plan_packages')) {
            return;
        }
        $this->dropLegacyPrefixedTables();
    }

    private function dropLegacyPrefixedTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'treatment_plan_addon_specialities',
            'treatment_plan_non_addon_links',
            'treatment_plan_addon_links',
            'treatment_plan_followup_links',
            'treatment_plan_stratifications',
            'treatment_plan_stratification_categories',
            'treatment_plan_implants',
            'treatment_plan_procedures',
            'treatment_plan_procedure_categories',
            'treatment_plan_investigations',
            'treatment_plan_packages',
        ] as $t) {
            Schema::dropIfExists($t);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
