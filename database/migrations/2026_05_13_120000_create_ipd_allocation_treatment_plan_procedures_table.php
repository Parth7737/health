<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * IPD bed allocation treatment plan lines (Patient 360).
     * Snapshots support billing/charges later via patient_charge_id / billed_at.
     */
    public function up(): void
    {
        Schema::create('ipd_allocation_treatment_plan_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bed_allocation_id');
            $table->unsignedBigInteger('hospital_id')->index();
            $table->unsignedSmallInteger('line_order')->default(0);

            $table->unsignedBigInteger('speciality_id')->nullable()->index();
            $table->unsignedBigInteger('procedure_id')->nullable()->index();
            $table->unsignedBigInteger('implant_id')->nullable()->index();
            $table->unsignedBigInteger('stratification_id')->nullable()->index();

            $table->string('speciality_name', 255)->nullable();
            $table->text('procedure_label')->nullable();
            $table->string('implant_label', 512)->nullable();
            $table->string('implant_qty', 32)->nullable();
            $table->string('stratification_label', 512)->nullable();
            $table->string('no_of_days', 64)->nullable();

            $table->decimal('amount_value', 14, 2)->default(0);
            $table->boolean('is_unverified_price')->default(false);
            $table->decimal('u100_amount', 14, 2)->nullable();

            $table->string('ichi_code', 128)->nullable();

            $table->unsignedBigInteger('patient_charge_id')->nullable()->index();
            $table->timestamp('billed_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['bed_allocation_id', 'line_order'], 'ipd_alloc_tp_proc_alloc_line_idx');

            $table->foreign('bed_allocation_id', 'ipd_alloc_tp_proc_bed_alloc_fk')
                ->references('id')
                ->on('bed_allocations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_allocation_treatment_plan_procedures');
    }
};
