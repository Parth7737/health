<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Align packages, investigations, procedure_categories, procedures, implants
 * with SHA migrations (create + follow-up alters).
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->expandPackages();
        $this->expandInvestigations();
        $this->expandProcedureCategories();
        $this->expandProcedures();
        $this->expandImplants();
    }

    public function down(): void
    {
        // Not reversing wide SHA column set.
    }

    private function expandPackages(): void
    {
        if (! Schema::hasTable('packages')) {
            return;
        }
        try {
            DB::statement('ALTER TABLE `packages` MODIFY `name` TEXT NULL');
            DB::statement('ALTER TABLE `packages` MODIFY `code` VARCHAR(255) NULL');
        } catch (\Throwable $e) {
        }
    }

    private function expandInvestigations(): void
    {
        if (! Schema::hasTable('investigations')) {
            return;
        }
        try {
            DB::statement('ALTER TABLE `investigations` MODIFY `name` TEXT NULL');
            DB::statement('ALTER TABLE `investigations` MODIFY `code` VARCHAR(255) NULL');
        } catch (\Throwable $e) {
        }

        Schema::table('investigations', function (Blueprint $table) {
            if (! Schema::hasColumn('investigations', 'scheme_type_id')) {
                $table->unsignedBigInteger('scheme_type_id')->nullable()->after('code');
            }
            if (! Schema::hasColumn('investigations', 'type')) {
                $table->string('type')->nullable()->after('scheme_type_id');
            }
            if (! Schema::hasColumn('investigations', 'is_required')) {
                $table->string('is_required')->nullable();
            }
        });
    }

    private function expandProcedureCategories(): void
    {
        if (! Schema::hasTable('procedure_categories')) {
            return;
        }
        try {
            DB::statement('ALTER TABLE `procedure_categories` MODIFY `name` VARCHAR(255) NULL');
            DB::statement('ALTER TABLE `procedure_categories` MODIFY `code` VARCHAR(255) NULL');
        } catch (\Throwable $e) {
        }
    }

    private function expandProcedures(): void
    {
        if (! Schema::hasTable('procedures')) {
            return;
        }

        Schema::table('procedures', function (Blueprint $table) {
            if (! Schema::hasColumn('procedures', 'scheme_type_id')) {
                $table->unsignedBigInteger('scheme_type_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('procedures', 'procedure_code') && ! Schema::hasColumn('procedures', 'procedure_code_1')) {
            DB::statement('ALTER TABLE `procedures` CHANGE `procedure_code` `procedure_code_1` VARCHAR(128) NULL');
        }

        if (! Schema::hasColumn('procedures', 'procedure_code_1')) {
            Schema::table('procedures', function (Blueprint $table) {
                $table->string('procedure_code_1', 128)->nullable()->after('package_id');
            });
        }

        Schema::table('procedures', function (Blueprint $table) {
            if (! Schema::hasColumn('procedures', 'procedure_code_2')) {
                $table->string('procedure_code_2', 128)->nullable()->after('procedure_code_1');
            }
            if (! Schema::hasColumn('procedures', 'is_multiple_procedure')) {
                $table->string('is_multiple_procedure')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'procedure_name')) {
                $table->text('procedure_name')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'icd_code')) {
                $table->string('icd_code')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'procedure_type')) {
                $table->string('procedure_type')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'non_nabh_price')) {
                $table->double('non_nabh_price', 24, 2)->nullable();
            }
            if (! Schema::hasColumn('procedures', 'stratification_criteria')) {
                $table->string('stratification_criteria')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'no_of_stratification')) {
                $table->string('no_of_stratification')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'implants_high_end_consumables')) {
                $table->string('implants_high_end_consumables')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'more_than_one_implant')) {
                $table->string('more_than_one_implant')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'special_conditions')) {
                $table->string('special_conditions')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'reservation_public_hospitals')) {
                $table->string('reservation_public_hospitals')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'reservation_tertiary_hospitals')) {
                $table->string('reservation_tertiary_hospitals')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'level_of_care')) {
                $table->string('level_of_care')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'los')) {
                $table->string('los')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'auto_approved')) {
                $table->string('auto_approved')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'mandatory_documents_pre_auth')) {
                $table->text('mandatory_documents_pre_auth')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'mandatory_documents_claim_processing')) {
                $table->text('mandatory_documents_claim_processing')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'procedure_label')) {
                $table->string('procedure_label')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'special_condition_pop_up')) {
                $table->string('special_condition_pop_up')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'special_condition_pop_up_message')) {
                $table->text('special_condition_pop_up_message')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'special_conditions_rule')) {
                $table->string('special_conditions_rule')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'special_conditions_rule_message')) {
                $table->text('special_conditions_rule_message')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'enhancement_applicable')) {
                $table->string('enhancement_applicable')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'medical_or_surgical')) {
                $table->string('medical_or_surgical')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'day_care_procedure')) {
                $table->string('day_care_procedure')->nullable();
            }
            if (! Schema::hasColumn('procedures', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        try {
            DB::statement('ALTER TABLE `procedures` MODIFY `price` DOUBLE(24,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `procedures` MODIFY `status` VARCHAR(64) NULL');
        } catch (\Throwable $e) {
        }
    }

    private function expandImplants(): void
    {
        if (! Schema::hasTable('implants')) {
            return;
        }
        if (! Schema::hasColumn('implants', 'speciality_id')) {
            Schema::table('implants', function (Blueprint $table) {
                $table->unsignedBigInteger('speciality_id')->nullable()->after('name');
            });
        }

        $fk = 'fk_imp_spec';
        $exists = DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            ['implants', $fk]
        );
        if ($exists && (int) $exists->c === 0) {
            Schema::table('implants', function (Blueprint $table) use ($fk) {
                $table->foreign('speciality_id', $fk)->references('id')->on('specialities')->nullOnDelete();
            });
        }
    }
};
