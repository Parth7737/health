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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('hfr_id');
            $table->string('user_id');
            $table->string('scheme')->nullable();
            $table->string('facility_name');
            $table->string('facility_type');
            $table->string('facility_speciality_type');
            $table->string('facility_ownership_type');
            $table->string('facility_ownership_sub_type1');
            $table->string('facility_ownership_sub_type2');
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
            $table->enum('status', ['Draft', 'Submitted', 'Queried', 'Empanelled', 'Rejected', 'Withdrawn', 'In-Active', 'Re-Empanelled', 'Empanelment Recommended by DEC', 'Response Required From Facility', 'Empanelment Not Recommended by DEC', 'Query Raised by SEC', 'Upgradation Request', 'Re-Submitted', 'Query Replied', 'De-Empanelled', 'Approved Upgradation Request', 'Query On Upgradation Request From Facility', 'Rejected Upgradation Request'])->default('Draft');   
            $table->datetime('status_update_date')->nullable();         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
