<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preauth_diagnosis_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('preauth_reference_options', function (Blueprint $table) {
            $table->id();
            $table->string('category', 64)->index();
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('preauth_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->index();
            $table->string('register_id');
            $table->unsignedInteger('benificiary_id')->nullable()->default(0);
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->unsignedBigInteger('bed_allocation_id')->nullable()->index();
            $table->unsignedBigInteger('sha_preauth_register_id')->nullable()->unique();
            $table->unsignedBigInteger('scheme_id')->nullable()->index();
            $table->string('grade')->nullable();
            $table->string('pincode')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->string('state_name')->nullable();
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
            $table->integer('status')->default(0);
            $table->dateTime('preauth_submission_date')->nullable();
            $table->dateTime('preauth_approved_date')->nullable();
            $table->dateTime('claim_submited_date')->nullable();
            $table->dateTime('claim_approved_date')->nullable();
            $table->dateTime('claim_aco_approved_date')->nullable();
            $table->dateTime('claim_paid_date')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('cancel_remarks')->nullable();
            $table->text('preauth_approve_remarks')->nullable();
            $table->integer('preauth_approve_reject_query_by')->nullable();
            $table->string('discharge_type')->nullable();
            $table->string('discharge_stage')->nullable();
            $table->date('lama_date')->nullable();
            $table->date('surgery_date')->nullable();
            $table->date('death_date')->nullable();
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
            $table->double('claim_amount', 24, 2)->nullable();
            $table->string('hospital_bill')->nullable();
            $table->string('claim_other_doc')->nullable();
            $table->text('claim_approve_remarks')->nullable();
            $table->integer('claim_approve_reject_query_by')->nullable();
            $table->double('preauth_initiated_amount', 24, 2)->nullable();
            $table->double('preauth_approved_amount', 24, 2)->nullable();
            $table->double('preauth_amount_without_deduction', 24, 2)->nullable();
            $table->double('deduction_discharge_amount', 24, 2)->nullable();
            $table->string('deduction_discharge_text')->nullable();
            $table->unsignedTinyInteger('is_new_born_baby')->default(0);
            $table->date('born_baby_dob')->nullable();
            $table->string('born_baby_name')->nullable();
            $table->string('born_baby_gender')->nullable();
            $table->string('born_baby_birth_certificate')->nullable();
            $table->unsignedTinyInteger('is_resubmit_done')->default(0);
            $table->timestamps();
        });

        Schema::create('general_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->string('temprature')->nullable();
            $table->string('pulserate')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('bmi')->nullable();
            $table->string('cyanosis')->nullable();
            $table->string('pallor')->nullable();
            $table->string('malnutration')->nullable();
            $table->string('oedema')->nullable();
            $table->timestamps();
        });

        Schema::create('family_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedInteger('diabetes_id')->nullable();
            $table->unsignedInteger('hypertension_id')->nullable();
            $table->unsignedInteger('heartdisease_id')->nullable();
            $table->unsignedInteger('stroke_id')->nullable();
            $table->unsignedInteger('cancer_id')->nullable();
            $table->unsignedInteger('tuberculosis_id')->nullable();
            $table->unsignedInteger('asthma_id')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedInteger('appetite_id')->nullable();
            $table->unsignedInteger('bowels_id')->nullable();
            $table->unsignedInteger('nutrition_id')->nullable();
            $table->unsignedInteger('diet_id')->nullable();
            $table->string('known_allergies')->nullable();
            $table->string('allergy_detail')->nullable();
            $table->string('habits')->nullable();
            $table->string('habits_detail')->nullable();
            $table->timestamps();
        });

        Schema::create('authentication_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->string('hospital_declaration_form')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->date('admission_date')->nullable();
            $table->date('surgery_date')->nullable();
            $table->string('admission_type_id')->nullable();
            $table->string('legal_case')->nullable();
            $table->string('fir_doc')->nullable();
            $table->timestamps();
        });

        Schema::create('preauth_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedBigInteger('diagnosis_id')->nullable();
            $table->string('diagnosis_type')->nullable();
            $table->string('other_diagnosis')->nullable();
            $table->timestamps();
        });

        Schema::create('preauth_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedBigInteger('procedure_id')->nullable();
            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->string('no_of_days')->nullable();
            $table->unsignedBigInteger('stratification_id')->nullable();
            $table->double('stratification_price', 24, 2)->default(0);
            $table->unsignedBigInteger('implant_id')->nullable();
            $table->unsignedInteger('implant_qty')->nullable();
            $table->double('implant_price', 24, 2)->default(0);
            $table->double('procedure_price', 24, 2)->default(0);
            $table->double('original_price', 24, 2)->nullable();
            $table->double('incentive', 24, 2)->default(0);
            $table->integer('incentive_per')->default(0);
            $table->integer('adj_per')->nullable();
            $table->text('deduction_reason')->nullable();
            $table->double('deducted_amount', 24, 2)->default(0);
            $table->text('deduction_remarks')->nullable();
            $table->text('preauth_status')->nullable();
            $table->text('preauth_reason')->nullable();
            $table->string('preauth_implant_status')->nullable();
            $table->string('preauth_implant_reason')->nullable();
            $table->text('preauth_claim_reason')->nullable();
            $table->text('preauth_claim_status')->nullable();
            $table->string('preauth_claim_implant_status')->nullable();
            $table->string('preauth_claim_implant_reason')->nullable();
            $table->integer('is_enhancement')->default(0);
            $table->integer('is_resubmission')->default(0);
            $table->integer('is_resubmission_delete')->default(0);
            $table->timestamps();
        });

        Schema::create('preauth_care_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedBigInteger('hospital_team_id')->nullable();
            $table->timestamps();
        });

        Schema::create('preauth_investigations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->index();
            $table->unsignedBigInteger('investigation_id');
            $table->string('file')->nullable();
            $table->integer('is_enhancement')->default(0);
            $table->integer('is_resubmission')->default(0);
            $table->integer('is_resubmission_delete')->default(0);
            $table->string('medical_committee_status')->nullable();
            $table->timestamps();
        });

        Schema::create('preauth_enhancement_docs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preauth_register_id')->nullable()->index();
            $table->string('temp_enhancement_id')->nullable();
            $table->string('name')->nullable();
            $table->string('file')->nullable();
            $table->integer('is_draft')->default(1);
            $table->text('ppd_status')->nullable();
            $table->dateTime('ppd_status_verify_date')->nullable();
            $table->text('cex_status')->nullable();
            $table->dateTime('cex_status_verify_date')->nullable();
            $table->text('cpd_status')->nullable();
            $table->dateTime('cpd_status_verify_date')->nullable();
            $table->text('sha_status')->nullable();
            $table->dateTime('sha_status_verify_date')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('preauth_diagnosis_masters')->insert([
            ['name' => 'Other', 'code' => 'OTHER', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Acute myocardial infarction', 'code' => 'I21', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pneumonia', 'code' => 'J18', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Appendicitis', 'code' => 'K35', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $opts = function (string $category, array $labels) use ($now) {
            $rows = [];
            $i = 0;
            foreach ($labels as $label) {
                $rows[] = [
                    'category' => $category,
                    'label' => $label,
                    'sort_order' => $i++,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            return $rows;
        };

        DB::table('preauth_reference_options')->insert(array_merge(
            $opts('Diabetes', ['Yes', 'No', 'Unknown']),
            $opts('Hypertension', ['Yes', 'No', 'Unknown']),
            $opts('HeartDisease', ['Yes', 'No', 'Unknown']),
            $opts('Stroke', ['Yes', 'No', 'Unknown']),
            $opts('Cancer', ['Yes', 'No', 'Unknown']),
            $opts('Tuberculosis', ['Yes', 'No', 'Unknown']),
            $opts('Asthma', ['Yes', 'No', 'Unknown']),
            $opts('Appetite', ['Normal', 'Increased', 'Decreased']),
            $opts('Bowels', ['Regular', 'Constipation', 'Diarrhoea']),
            $opts('Nutrition', ['Good', 'Fair', 'Poor']),
            $opts('Diet', ['Vegetarian', 'Non-vegetarian', 'Mixed']),
            $opts('AdmissionType', ['Planned', 'Emergency']),
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('preauth_enhancement_docs');
        Schema::dropIfExists('preauth_investigations');
        Schema::dropIfExists('preauth_care_teams');
        Schema::dropIfExists('preauth_procedures');
        Schema::dropIfExists('preauth_diagnoses');
        Schema::dropIfExists('admission_details');
        Schema::dropIfExists('authentication_consents');
        Schema::dropIfExists('personal_histories');
        Schema::dropIfExists('family_histories');
        Schema::dropIfExists('general_infos');
        Schema::dropIfExists('preauth_registers');
        Schema::dropIfExists('preauth_reference_options');
        Schema::dropIfExists('preauth_diagnosis_masters');
    }
};
