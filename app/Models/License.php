<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = ['name'];

    public function licenseType()
    {
        return $this->hasMany(LicenseType::class, 'license_id', 'id');
    }

}
