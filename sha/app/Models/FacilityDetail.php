<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityDetail extends Model
{
    use HasFactory;

    protected $table = 'facility_details';

    protected $fillable = [
        'facility_id',
        'facility_name',
        'state',
        'district',
        'sub_district',
        'facility_ownership',
    ];
}
