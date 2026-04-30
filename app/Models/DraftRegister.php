<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftRegister extends Model
{
    protected $fillable = ['name', 'uuid', 'email', 'userid', 'aadhaar_no', 'kyc_mode', 'otp', 'gender', 'state','district' , 'avatar', 'mobile_no', 'nature_of_employment', 'designation', 'parent_entity', 'entity_type', 'entity_name', 'user_role', 'is_approve', 'password', 'register_status', 'age', 'user_id'];

    public function scopeEmail($query, $email) {
        return $query->where('email', $email);
    }
    public function scopeAadhaar($query, $aadhaar) {
        return $query->where('aadhaar_no', $aadhaar);
    }
    public function role() {
        return $this->belongsTo(Role::class, 'user_role');
    }
}
