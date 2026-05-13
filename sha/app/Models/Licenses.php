<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenses extends Model
{
    protected $fillable = ['name'];

    public function licenseType()
{
    return $this->hasMany(LicensesType::class, 'license_id', 'id');
}

}
