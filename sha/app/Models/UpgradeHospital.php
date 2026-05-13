<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeHospital extends Model
{
    protected $fillable = ['hospital_id', 'establishment_details', 'address', 'scheme', 'speciality', 'services', 'statutory_licences', 'human_resources', 'quality_accreditation', 'financial_information', 'tax_details'];
}
