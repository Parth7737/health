<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'is_custom',
        'entity',
        'hospital_id',
        'guard_name',
    ];
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }
    public function scopeHospitalRole($query)
    {
        $hospitalId = auth()->user()?->hospital_id;

        return $query->where('name', '!=', 'Master Admin')
            ->where(function ($q) use ($hospitalId) {
                $q->whereNull('hospital_id');
                if (!empty($hospitalId)) {
                    $q->orWhere('hospital_id', $hospitalId);
                }
            });
    }
}
