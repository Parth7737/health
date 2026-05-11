<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_recruitment_vacancies')) {
            Schema::table('hr_recruitment_vacancies', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_recruitment_vacancies', 'hr_designation_id')) {
                    $table->foreignId('hr_designation_id')->nullable()->after('department_id')->constrained('hr_designations')->nullOnDelete();
                }
                if (!Schema::hasColumn('hr_recruitment_vacancies', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('hr_recruitment_vacancies', 'open_from')) {
                    $table->date('open_from')->nullable()->after('status');
                }
                if (!Schema::hasColumn('hr_recruitment_vacancies', 'open_till')) {
                    $table->date('open_till')->nullable()->after('open_from');
                }
                if (!Schema::hasColumn('hr_recruitment_vacancies', 'is_published')) {
                    $table->boolean('is_published')->default(true)->after('open_till');
                }
            });
        }

        if (!Schema::hasTable('hr_recruitment_applications')) {
            Schema::create('hr_recruitment_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id');
                $table->unsignedBigInteger('hr_recruitment_vacancy_id');
                $table->string('full_name', 150);
                $table->string('email', 150);
                $table->string('phone', 25)->nullable();
                $table->string('resume_path')->nullable();
                $table->text('cover_letter')->nullable();
                $table->enum('status', ['Applied', 'Screening', 'Shortlisted', 'Interview', 'Selected', 'Rejected', 'Hired'])->default('Applied');
                $table->text('status_note')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->foreign('hospital_id', 'hra_hospital_fk')
                    ->references('id')->on('hospitals')->onDelete('cascade');
                $table->foreign('hr_recruitment_vacancy_id', 'hra_vacancy_fk')
                    ->references('id')->on('hr_recruitment_vacancies')->onDelete('cascade');
                $table->index(['hospital_id', 'status'], 'hra_hosp_status_idx');
                $table->index(['hospital_id', 'hr_recruitment_vacancy_id'], 'hra_hosp_vac_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hr_recruitment_applications')) {
            Schema::drop('hr_recruitment_applications');
        }

        if (Schema::hasTable('hr_recruitment_vacancies')) {
            Schema::table('hr_recruitment_vacancies', function (Blueprint $table) {
                if (Schema::hasColumn('hr_recruitment_vacancies', 'is_published')) {
                    $table->dropColumn('is_published');
                }
                if (Schema::hasColumn('hr_recruitment_vacancies', 'open_till')) {
                    $table->dropColumn('open_till');
                }
                if (Schema::hasColumn('hr_recruitment_vacancies', 'open_from')) {
                    $table->dropColumn('open_from');
                }
                if (Schema::hasColumn('hr_recruitment_vacancies', 'description')) {
                    $table->dropColumn('description');
                }
                if (Schema::hasColumn('hr_recruitment_vacancies', 'hr_designation_id')) {
                    $table->dropConstrainedForeignId('hr_designation_id');
                }
            });
        }
    }
};
