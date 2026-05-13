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
        Schema::create('u_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('hfr_id');
            $table->string('user_id');
            $table->string('scheme')->nullable();
            $table->string('facility_name');
            $table->string('facility_type');
            $table->string('facility_speciality_type');
            $table->string('facility_ownership_type');
            $table->string('sub_type_certificate_name')->nullable();
            $table->text('sub_type_certificate')->nullable();
            $table->string('facility_ownership_sub_type1');
            $table->string('facility_ownership_sub_type2');
            $table->string('facility_ownership_sub_type3')->nullable();
            $table->string('date_of_establishment');
            $table->string('facility_registration_certificate');
            $table->string('facility_registration_number');
            $table->string('registration_certificate_expiry');
            $table->string('system_of_medicine');
            $table->string('gov_benifits')->nullable();
            $table->string('rohini_id')->nullable();
            $table->string('group_id')->nullable();
            $table->string('name_od_group')->nullable();
            $table->string('pg_dnb')->nullable();  
            $table->string('house_keeping')->nullable();     
            $table->string('medico_count')->nullable();     
            $table->tinyInteger('is_approve')->default(0);            
            $table->tinyInteger('is_added')->default(0);            
            $table->string('step')->default('1');            
            $table->text('remark')->nullable();            
            $table->enum('status', ['Draft', 'Submitted', 'Queried', 'Empanelled', 'Rejected', 'Withdrawn', 'In-Active', 'Re-Empanelled', 'Empanelment Recommended by DEC', 'Response Required From Facility', 'Empanelment Not Recommended by DEC', 'Query Raised by SEC', 'Upgradation Request','Re-Submitted', 'Query Replied', 'Approved Upgradation Request', 'Query On Upgradation Request From Facility', 'Rejected Upgradation Request'])->default('Draft');   
            $table->datetime('status_update_date')->nullable();  
            $table->string('dec_verify_status')->nullable();
            $table->text('dec_verify_remark')->nullable();
            $table->bigInteger('dec_verify_id')->nullable();
            $table->string('dec_status')->nullable();
            $table->text('dec_remark')->nullable();
            $table->bigInteger('dec_id')->nullable();
            $table->string('sec_status')->nullable();
            $table->text('sec_remark')->nullable();
            $table->bigInteger('sec_id')->nullable();
            $table->text('image')->nullable();
            $table->text('hospital_ppt')->nullable();
            $table->string('hospital_id')->nullable();
            $table->string('propritership_document_name')->nullable();
            $table->text('propritership_document')->nullable();
            $table->text('hospital_registration_certificate')->nullable();
            $table->string('total_no_of_beds')->nullable();
            $table->boolean('is_declaration')->default(0);
            $table->string('dec_qry_id')->nullable();
            $table->string('qry_type')->nullable();
            $table->string('dec_work_id')->nullable();
            $table->date('dec_change_date')->nullable();
            $table->string('sec_qry_id')->nullable();
            $table->string('sec_type')->nullable();
            $table->string('sec_work_id')->nullable();
            $table->date('sec_change_date')->nullable();
            $table->boolean('is_empanelled')->default(0); 
            $table->bigInteger('main_hospitalid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_hospitals');
    }
};
