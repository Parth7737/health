<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthRegister extends Model
{
    public const STATUS_REGISTER = 0;
    public const STATUS_PREAUTH_PENDING = 1;
    public const STATUS_PREAUTH_APPROVED = 2;
    public const STATUS_PREAUTH_REJECTED = 3;
    public const STATUS_PREAUTH_QUERIED = 4;
    public const STATUS_CLAIM_SUBMITTED = 5;
    public const STATUS_CLAIM_PENDING = 6;
    public const STATUS_CLAIM_APPROVED = 7;
    public const STATUS_CLAIM_REJECTED = 8;
    public const STATUS_CLAIM_QUERIED = 9;
    public const STATUS_CANCELLED = 10;
    public const STATUS_PREAUTH_CANCELLED = 11;
    public const STATUS_DISCHARGE = 12;
    public const STATUS_CPD_CLAIM_PENDING = 13;
    public const STATUS_ACO_CLAIM_APPROVED = 14;
    public const STATUS_ACO_CLAIM_REJECTED = 15;
    public const STATUS_PAYMENT_REINITIATE_BY_ACO = 20;
    public const STATUS_ACO_CLAIM_QUERIED = 16;
    public const STATUS_SHA_CLAIM_APPROVED = 17;
    public const STATUS_SHA_CLAIM_REJECTED = 18;
    public const STATUS_SHA_CLAIM_QUERIED = 19;
    public const STATUS_CLAIM_SENT_TO_BANK = 21;
    public const STATUS_CLAIM_PAID_BY_BANK = 22;
    public const STATUS_PAYMENT_REJECTED_BY_BANK = 23;
    public const STATUS_ERRONEOUS_CLAIM_PENDING = 24;
    public const STATUS_ERRONEOUS_CLAIM_APPROVED = 25;
    public const STATUS_ERRONEOUS_CLAIM_QUERIED = 26;
    public const STATUS_ERRONEOUS_CLAIM_REJECTED = 27;
    public const STATUS_ERRONEOUS_CLAIM_PAID = 28;
    public const STATUS_ERRONEOUS_ACO_CLAIM_APPROVED = 29;
    public const STATUS_ERRONEOUS_ACO_CLAIM_REJECTED = 30;
    public const STATUS_ERRONEOUS_ACO_CLAIM_QUERIED = 31;
    public const STATUS_ERRONEOUS_SHA_CLAIM_APPROVED = 32;
    public const STATUS_ERRONEOUS_SHA_CLAIM_REJECTED = 33;
    public const STATUS_ERRONEOUS_SHA_CLAIM_QUERIED = 34;
    public const STATUS_MEDICAL_COMMITTEE_PENDING = 35;
    public const STATUS_MEDICAL_COMMITTEE_APPROVED = 36;
    public const STATUS_MEDICAL_COMMITTEE_REJECTED = 37;
    public const STATUS_MEDICAL_COMMITTEE_QUERIED = 38;
    public const STATUS_CEO_APPROVED = 39;
    public const STATUS_CEO_REJECTED = 40;
    public const STATUS_CEO_QUERIED = 41;
    public const STATUS_ACS_PENDING = 42;
    public const STATUS_ACS_APPROVED = 43;
    public const STATUS_ACS_REJECTED = 44;
    public const STATUS_ACS_QUERIED = 45;

    public function getStatusLabelAttribute()
    {
        return self::getStatusLabelByValue($this->status);
    }

    public static function getStatusLabelByValue($status)
    {
        return self::statusLabels()[$status] ?? 'Unknown';
    }

    private static function statusLabels(): array
    {
        return [
            self::STATUS_REGISTER => 'Preauthorization be initiated',
            self::STATUS_PREAUTH_PENDING => 'Preauth Pending',
            self::STATUS_PREAUTH_APPROVED => 'Preauth Approved',
            self::STATUS_PREAUTH_REJECTED => 'Preauth Rejected',
            self::STATUS_PREAUTH_QUERIED => 'Preauth Queried',
            self::STATUS_CLAIM_SUBMITTED => 'Claim to be Submitted',
            self::STATUS_CLAIM_PENDING => 'Claim Pending',
            self::STATUS_CLAIM_APPROVED => 'Claim Approved',
            self::STATUS_CLAIM_REJECTED => 'Claim Rejected',
            self::STATUS_CLAIM_QUERIED => 'Claim Queried',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_PREAUTH_CANCELLED => 'Preauth Cancelled',
            self::STATUS_DISCHARGE => 'Discharge',
            self::STATUS_CPD_CLAIM_PENDING => 'CPD Claim Pending',
            self::STATUS_ACO_CLAIM_APPROVED => 'ACO Claim Approved',
            self::STATUS_ACO_CLAIM_QUERIED => 'ACO Claim Queried',
            self::STATUS_ACO_CLAIM_REJECTED => 'ACO Claim Rejected',
            self::STATUS_PAYMENT_REINITIATE_BY_ACO => 'Payment Re-initiate By ACO',
            self::STATUS_SHA_CLAIM_APPROVED => 'SHA Claim Approved',
            self::STATUS_SHA_CLAIM_REJECTED => 'SHA Claim Rejected',
            self::STATUS_SHA_CLAIM_QUERIED => 'SHA Claim Queried',
            self::STATUS_CLAIM_SENT_TO_BANK => 'Claim Sent to Bank',
            self::STATUS_CLAIM_PAID_BY_BANK => 'Claim Paid by Bank',
            self::STATUS_PAYMENT_REJECTED_BY_BANK => 'Payment Rejected By Bank',
            self::STATUS_ERRONEOUS_CLAIM_PENDING => 'Erroneous Claim Pending',
            self::STATUS_ERRONEOUS_CLAIM_APPROVED => 'Erroneous Claim Approved',
            self::STATUS_ERRONEOUS_CLAIM_QUERIED => 'Erroneous Claim Queried',
            self::STATUS_ERRONEOUS_CLAIM_REJECTED => 'Erroneous Claim Rejected',
            self::STATUS_ERRONEOUS_CLAIM_PAID => 'Erroneous Claim Paid',
            self::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED => 'Erroneous ACO Claim Approved',
            self::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED => 'Erroneous ACO Claim Queried',
            self::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED => 'Erroneous ACO Claim Rejected',
            self::STATUS_ERRONEOUS_SHA_CLAIM_APPROVED => 'Erroneous SHA Claim Approved',
            self::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED => 'Erroneous SHA Claim Rejected',
            self::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED => 'Erroneous SHA Claim Queried',
            self::STATUS_MEDICAL_COMMITTEE_PENDING => 'Medical Committee Pending',
            self::STATUS_MEDICAL_COMMITTEE_APPROVED => 'Medical Committee Approved',
            self::STATUS_MEDICAL_COMMITTEE_REJECTED => 'Medical Committee Rejected',
            self::STATUS_MEDICAL_COMMITTEE_QUERIED => 'Medical Committee Queried',
            self::STATUS_CEO_APPROVED => 'CEO Approved',
            self::STATUS_CEO_REJECTED => 'CEO Rejected',
            self::STATUS_CEO_QUERIED => 'CEO Queried',
            self::STATUS_ACS_PENDING => 'ACS/Chairman Pending',
            self::STATUS_ACS_APPROVED => 'ACS/Chairman Approved',
            self::STATUS_ACS_REJECTED => 'ACS/Chairman Rejected',
            self::STATUS_ACS_QUERIED => 'ACS/Chairman Queried',
        ];
    }

    public function benificiary() {
        return $this->belongsTo(Benificiary::class, 'benificiary_id');
    }
    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }
    public function accreditation() {
        return $this->belongsTo(HospitalAccreditation::class, 'hospital_id','hospital_id');
    }
    public function tax_detail() {
        return $this->belongsTo(TaxDetails::class, 'hospital_id','hospital_id');
    }
    public function adjustment() {
        return $this->belongsTo(Adjustment::class, 'id','preauth_register_id');
    }
    public function district() {
        return $this->belongsTo(HospitalDistrict::class, 'district_id');
    }
    public function state() {
        return $this->belongsTo(HospitalState::class, 'state_id');
    }
    public function investigations() {
        return $this->hasMany(PreauthInvestigation::class, 'preauth_register_id');
    }
    public function enhancement_docs() {
        return $this->hasMany(PreauthEnhancementDoc::class, 'preauth_register_id');
    }
    public function claim_investigations() {
        return $this->hasMany(PreauthClaimInvestigation::class, 'preauth_register_id');
    }

    public function tabs() {
        return $this->hasMany(VerifyTabs::class, 'preauth_register_id');
    }
}
