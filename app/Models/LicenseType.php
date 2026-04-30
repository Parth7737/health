<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    protected $fillable = ['name', 'license_id', 'is_required', 'document_required'];

    public function license()
    {
        return $this->belongsTo(License::class, 'license_id', 'id');
    }
}
