<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = ['uuid', 'user_id','code','parent_id','name','type_id','email','phone','address','city','pincode','landmark','is_approve','status','reject_reason','hospital_type','image'];

    public function scopeStatus($query, $status)
    {
        return $query->where('status', "$status");
    }

    public function chairman() {
        return $this->belongsTo('App\Models\User', 'id','hospital_id')->whereHas('roles', function ($query) { $query->where('name', 'Chairman');});
    }
    public function hospital_admin() {
        return $this->belongsTo('App\Models\User', 'id','hospital_id')->whereHas('roles', function ($query) { $query->where('name', 'Admin');});
    }
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function parent() {
        return $this->belongsTo('App\Models\Hospital', 'parent_id','id');
    }

    public function type() {
        return $this->belongsTo('App\Models\HospitalType','type_id');
    }
    public function documents() {
        return $this->hasMany('App\Models\HospitalDocument', 'hospital_id');
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

    public function branches() {
        return $this->hasMany('App\Models\Hospital', 'parent_id');
    }
}
