<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalRolePermissionOverride extends Model
{
    protected $fillable = [
        'hospital_id',
        'role_id',
        'permission_name',
        'is_allowed',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
