<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('edc_work_flows', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('action_id');
            $table->string('action');
            $table->text('remark');
            $table->date('date_of_issuance')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('action_start_date')->nullable();
            $table->date('action_end_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('fir_case_number')->nullable();
            $table->string('penalty_imposed')->nullable();
            $table->string('penalty_recovered')->nullable();
            $table->string('days')->nullable();
            $table->string('added_by')->nullable();
            $table->string('authority')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edc_work_flows');
    }
};
