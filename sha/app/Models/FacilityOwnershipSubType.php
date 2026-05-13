<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityOwnershipSubType extends Model
{
    protected $fillable = ['facility_ownership_type_id', 'name', 'type', 'type_id', 'type2_id'];

    public function ownershipType()
    {
        return $this->belongsTo(FacilityOwnershipType::class, 'facility_ownership_type_id');
    }
}
