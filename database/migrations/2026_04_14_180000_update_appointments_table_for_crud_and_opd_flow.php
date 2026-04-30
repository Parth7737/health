<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'source')) {
                $table->string('source')->default('Panel')->after('doctor_id');
            }

            if (!Schema::hasColumn('appointments', 'live_consultation')) {
                $table->enum('live_consultation', ['Yes', 'No'])->default('No')->after('source');
            }

            if (!Schema::hasColumn('appointments', 'opd_patient_id')) {
                $table->unsignedBigInteger('opd_patient_id')->nullable()->after('status');
            }

            if (!Schema::hasColumn('appointments', 'moved_to_opd_at')) {
                $table->timestamp('moved_to_opd_at')->nullable()->after('opd_patient_id');
            }
        });

        if (Schema::hasColumn('appointments', 'opd_patient_id')) {
            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('opd_patient_id')->references('id')->on('opd_patients')->onDelete('set null');
                });
            } catch (\Throwable $th) {
                // Ignore if FK already exists.
            }
        }

        if (Schema::hasColumn('appointments', 'doctor_id')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                $foreignKeys = DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointments' AND COLUMN_NAME = 'doctor_id' AND REFERENCED_TABLE_NAME IS NOT NULL"
                );

                foreach ($foreignKeys as $foreignKey) {
                    DB::statement('ALTER TABLE appointments DROP FOREIGN KEY ' . $foreignKey->CONSTRAINT_NAME);
                }
            }

            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('doctor_id')->references('id')->on('staff')->onDelete('set null');
                });
            } catch (\Throwable $th) {
                // Ignore if FK already exists.
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        if (Schema::hasColumn('appointments', 'opd_patient_id')) {
            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->dropForeign(['opd_patient_id']);
                });
            } catch (\Throwable $th) {
                // Ignore if FK not present.
            }
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'moved_to_opd_at')) {
                $table->dropColumn('moved_to_opd_at');
            }

            if (Schema::hasColumn('appointments', 'opd_patient_id')) {
                $table->dropColumn('opd_patient_id');
            }

            if (Schema::hasColumn('appointments', 'live_consultation')) {
                $table->dropColumn('live_consultation');
            }

            if (Schema::hasColumn('appointments', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
