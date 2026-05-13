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
        Schema::table('benificiaries', function (Blueprint $table) {
           $table->bigInteger('id_pk')->nullable();
           $table->string('ben_id')->nullable();
            $table->string('family_id')->nullable();
            $table->string('member_id')->nullable();
            $table->string('bis_family_id')->nullable();
            $table->string('bis_member_id')->nullable();
            $table->string('ben_ref_id')->nullable();
            $table->integer('state_cd')->nullable();
            $table->integer('dist_cd')->nullable();
            $table->integer('block_id')->nullable();
            $table->integer('village_id')->nullable();
            $table->string('rural_urban_flag')->nullable();
            $table->string('house_no')->nullable();
            $table->string('dist_name')->nullable();
            $table->string('state_name')->nullable();
            $table->string('ben_mobile_no')->nullable();
            $table->string('ben_email_id')->nullable();
            $table->string('json_obj_ben_source_dtl')->nullable();
            $table->string('json_obj_ben_ekyc_dtl')->nullable();
            $table->boolean('active_status')->default(1);
            $table->string('enrl_status')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->string('abha_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('tpa_isa_id')->nullable();
            $table->string('json_obj_ben_othr_dtl')->nullable();
            $table->string('src_flag')->nullable();
            $table->bigInteger('enrol_status')->nullable();
            $table->string('entity_id')->nullable();
            $table->string('card_no')->nullable();
            $table->string('photo')->nullable();
            $table->string('relation')->nullable();
            $table->string('auth_mode')->nullable();
            $table->string('primary_auth_mode')->nullable();
            $table->string('new_member_flag')->nullable();
            $table->string('year_of_birth')->nullable();
            $table->string('father_name')->nullable();
            $table->string('primary_ben_id')->nullable();
            $table->string('approve_date')->nullable();
            $table->string('enrol_date')->nullable();
            $table->string('card_status')->nullable();
            $table->string('aadhar_status')->nullable();
            $table->string('reject_date')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('scheme_code')->nullable();
            $table->string('request_type')->nullable();
            $table->string('auth_txn')->nullable();
            $table->string('primary_auth_txn')->nullable();
            $table->string('request_agent')->nullable();
            $table->string('match_score')->nullable();
            $table->string('source_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benificiaries', function (Blueprint $table) {
            //
        });
    }
};
