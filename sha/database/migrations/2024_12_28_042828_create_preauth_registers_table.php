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
        Schema::create('preauth_registers', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_id');
            $table->string('register_id');
            $table->integer('benificiary_id');
            $table->integer('pincode');
            $table->integer('state_id');
            $table->integer('district_id');
            $table->string('city')->nullable();
            $table->string('kyc_type')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->text('address')->nullable();
            $table->text('address_2')->nullable();
            $table->string('patient_type')->nullable();
            $table->string('attendant_patient')->nullable();
            $table->string('full_name')->nullable();
            $table->string('relationship')->nullable();
            $table->string('other_relation')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('hospital_declaration_form')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('status')->default(0)->commant('0=>register,1=>preauth_pending,2=>preauth_approved,3=>preauth_rejected,4=>preauth_queried,5=>claim_submited,6=>claim_pending,7=>claim_approved,8=>claim_rejected,9=>claim_queried,10=>cancelled,11=>preauth_cancelled,12=>discharge,13=>cpt_claim_pending');
            $table->datetime('preauth_submission_date')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('cancel_remarks')->nullable();
            $table->text('preauth_approve_remarks')->nullable();
            $table->integer('preauth_approve_reject_query_by')->nullable();
            $table->string('discharge_type')->nullable();
            $table->string('discharge_stage')->nullable();
            $table->date('lama_date')->nullable();
            $table->date('surgery_date')->nullable();
            $table->date('discharge_date')->nullable();
            $table->string('provide_medicine')->nullable();
            $table->string('death_certificate')->nullable();
            $table->string('death_summary')->nullable();
            $table->string('mortality_audit_report')->nullable();
            $table->string('in_treatment_photo')->nullable();
            $table->string('post_surgery_photo')->nullable();
            $table->string('discharge_summary')->nullable();
            $table->string('bill_no')->nullable();
            $table->date('bill_date')->nullable();
            $table->double('claim_amount',24,2)->nullable();
            $table->string('hospital_bill')->nullable();
            $table->string('claim_other_doc')->nullable();
            $table->text('claim_approve_remarks')->nullable();
            $table->integer('claim_approve_reject_query_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preauth_registers');
    }
};
