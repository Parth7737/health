<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InitiateVerification extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'verification_authority', 'physical_verifier', 'verification_type', 'date_of_assignment', 'due_date_of_physical_verification', 'status', 'is_approve', 'assigned_by'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function verifier() {
        return $this->belongsTo('App\Models\User', 'physical_verifier');
    }
}
