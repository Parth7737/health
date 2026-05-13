<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_recruitment_application_status_logs')) {
            return;
        }

        Schema::create('hr_recruitment_application_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('hr_recruitment_application_id');
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at');

            $table->foreign('hospital_id', 'hra_logs_hosp_fk')
                ->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('hr_recruitment_application_id', 'hra_logs_app_fk')
                ->references('id')->on('hr_recruitment_applications')->onDelete('cascade');
            $table->index(['hr_recruitment_application_id', 'created_at'], 'hra_logs_app_created_idx');
        });

        if (Schema::hasTable('hr_recruitment_applications')) {
            $apps = DB::table('hr_recruitment_applications')
                ->select('id', 'hospital_id', 'status', 'status_note', 'applied_at', 'created_at')
                ->orderBy('id')
                ->get();

            foreach ($apps as $app) {
                $has = DB::table('hr_recruitment_application_status_logs')
                    ->where('hr_recruitment_application_id', $app->id)
                    ->exists();
                if ($has) {
                    continue;
                }
                DB::table('hr_recruitment_application_status_logs')->insert([
                    'hospital_id' => (int) $app->hospital_id,
                    'hr_recruitment_application_id' => (int) $app->id,
                    'from_status' => null,
                    'to_status' => (string) ($app->status ?: 'Applied'),
                    'note' => $app->status_note ?: null,
                    'created_by' => null,
                    'created_at' => $app->applied_at ?: $app->created_at ?: now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_recruitment_application_status_logs');
    }
};
