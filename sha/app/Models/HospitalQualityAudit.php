<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalQualityAudit extends Model
{
    protected $fillable = ['hospital_id', 'category_id', 'sub_category_id', 'audit_id', 'action', 'year', 'month', 'submitted_date'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function category() {
        return $this->belongsTo('App\Models\AuditCategory', 'category_id');
    }

    public function subcategory() {
        return $this->belongsTo('App\Models\AuditSubCategory', 'sub_category_id');
    }

    public function audit() {
        return $this->belongsTo('App\Models\AuditList', 'audit_id');
    }
}
