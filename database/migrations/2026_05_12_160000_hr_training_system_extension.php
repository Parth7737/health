<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_training_programs')) {
            Schema::table('hr_training_programs', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_training_programs', 'description')) {
                    $table->text('description')->nullable()->after('trainer_name');
                }
                if (!Schema::hasColumn('hr_training_programs', 'venue')) {
                    $table->string('venue', 191)->nullable()->after('description');
                }
                if (!Schema::hasColumn('hr_training_programs', 'duration_hours')) {
                    $table->unsignedSmallInteger('duration_hours')->nullable()->after('venue');
                }
            });
        }

        if (!Schema::hasTable('hr_training_participants')) {
            Schema::create('hr_training_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
                $table->foreignId('hr_training_program_id')->constrained('hr_training_programs')->onDelete('cascade');
                $table->unsignedBigInteger('staff_id');
                $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
                $table->string('certificate_number', 80)->nullable()->unique();
                $table->string('certificate_path', 255)->nullable();
                $table->timestamp('certificate_issued_at')->nullable();
                $table->timestamps();
                $table->unique(['hr_training_program_id', 'staff_id'], 'hr_training_participant_unique');
                $table->index(['hospital_id', 'hr_training_program_id'], 'hr_training_participants_hosp_prog_idx');
            });
        }

        if (!Schema::hasTable('hr_training_program_logs')) {
            Schema::create('hr_training_program_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
                $table->foreignId('hr_training_program_id')->constrained('hr_training_programs')->onDelete('cascade');
                $table->string('event_type', 50);
                $table->text('message');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['hr_training_program_id', 'created_at'], 'hr_training_logs_prog_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_training_program_logs');
        Schema::dropIfExists('hr_training_participants');

        if (Schema::hasTable('hr_training_programs')) {
            Schema::table('hr_training_programs', function (Blueprint $table) {
                foreach (['duration_hours', 'venue', 'description'] as $col) {
                    if (Schema::hasColumn('hr_training_programs', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
