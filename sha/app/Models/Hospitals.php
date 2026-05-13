<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospitals extends Model
{
    protected $fillable = ['uuid', 'user_id', 'hfr_id', 'scheme', 'facility_name', 'facility_type', 'facility_speciality_type', 'facility_ownership_type', 'facility_ownership_sub_type1', 'facility_ownership_sub_type2', 'date_of_establishment', 'facility_registration_certificate', 'facility_registration_number', 'registration_certificate_expiry', 'system_of_medicine', 'gov_benifits', 'rohini_id', 'group_id', 'name_od_group', 'pg_dnb', 'is_approve', 'is_added', 'status', 'remark', 'step', 'medico_count', 'house_keeping', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'status_update_date', 'mobile_no', 'hospital_id', 'facility_ownership_sub_type3', 'sub_type_certificate_name', 'sub_type_certificate', 'hospital_ppt', 'total_no_of_beds', 'propritership_document_name', 'propritership_document', 'hospital_registration_certificate', 'is_declaration', 'dec_qry_id', 'qry_type', 'dec_work_id', 'dec_change_date', 'sec_change_date', 'sec_work_id', 'sec_type', 'sec_qry_id', 'is_empanelled', 'is_upgrade_application', 'is_payment_stop', 'is_preauth_stop', 'exists_hospital_id'];

    public function hospitalAddress() {
        return $this->hasone('App\Models\HospitalAddress', 'hospital_id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', "$status");
    }

    public function facilityType() {
        return $this->belongsTo('App\Models\FacilityType', 'facility_type');
    }

    public function schemeType() {
        return $this->belongsTo('App\Models\SchemeType', 'scheme');
    }

    public function facilitySpecialityType() {
        return $this->belongsTo('App\Models\FacilitySpecialityType', 'facility_speciality_type');
    }

    public function facilityOwnershipType() {
        return $this->belongsTo('App\Models\FacilityOwnershipType', 'facility_ownership_type');
    }

    public function facilityOwnershipSubType1() {
        return $this->belongsTo('App\Models\FacilityOwnershipSubType', 'facility_ownership_sub_type1');
    }

    public function facilityOwnershipSubType2() {
        return $this->belongsTo('App\Models\FacilityOwnershipSubType', 'facility_ownership_sub_type2');
    }

    public function facilityOwnershipSubType3() {
        return $this->belongsTo('App\Models\FacilityOwnershipSubType', 'facility_ownership_sub_type3');
    }

    public function getFacilityOwnershipSubType3ValueAttribute()
    {
        if (is_string($this->facilityOwnershipSubType)) { // Some condition to decide
            return $this->facility_ownership_sub_type3; // Return full relation
        } else {
            return $this->facilityOwnershipSubType3->name; // Return just the name or another attribute
        }
    }

    public function facilityRegistrationCertificate() {
        return $this->belongsTo('App\Models\FacilityRegistrationCertificate', 'facility_registration_certificate');
    }

    public function systemMedicine() {
        return $this->belongsTo('App\Models\SystemMedicine', 'system_of_medicine');
    }

    public function govermentBenefits() {
        return $this->belongsTo('App\Models\GovermentBenefits', 'gov_benifits');
    }

    public function specialities() {
        return $this->hasMany('App\Models\HospitalSpeciality', 'hospital_id');
    }

    public function services() {
        return $this->hasMany('App\Models\HospitalServices', 'hospital_id');
    }

    public function licenses() {
        return $this->hasMany('App\Models\HospitalLicense', 'hospital_id');
    }

    public function ceo() {
        return $this->hasOne('App\Models\HospitalCeo', 'hospital_id');
    }
    public function humanResources() {
        return $this->hasMany('App\Models\HospitalHumanResource', 'hospital_id');
    }

    public function hospitalTeam() {
        return $this->hasMany('App\Models\HospitalTeam', 'hospital_id');
    }
    public function hospitalAccreditation() {
        return $this->hasOne('App\Models\HospitalAccreditation', 'hospital_id');
    }
    public function financialInformation() {
        return $this->hasOne('App\Models\FinancialInformation', 'hospital_id');
    }
    public function taxDetails() {
        return $this->hasOne('App\Models\TaxDetails', 'hospital_id');
    }
    
    public function documents() {
        return $this->hasMany('App\Models\HospitalDocument', 'hospital_id');
    }
    
    public function payments() {
        return $this->hasMany('App\Models\Payment', 'hospital_id');
    }

    public function initiateVerifications() {
        return $this->hasMany('App\Models\InitiateVerification', 'hospital_id');
    }

    public function workFlowHistories() {
        return $this->hasMany('App\Models\WorkFlowHistory', 'hospital_id');
    }

    public function hospitalReport() {
        return $this->hasOne('App\Models\HospitalReport', 'hospital_id');
    }

    public function images() {
        return $this->hasMany('App\Models\HospitalImages', 'hospital_id');
    }

    public function upgradeHospital() {
        return $this->hasOne('App\Models\UpgradeHospital', 'hospital_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function annualdeclaration() {
        return $this->hasMany('App\Models\AnnualDeclaration', 'hospital_id');
    }

    public function qualityAudit() {
        return $this->hasMany('App\Models\HospitalQualityAudit', 'hospital_id');
    }
}
