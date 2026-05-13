<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicensesType extends Model
{
    protected $fillable = ['name', 'license_id', 'is_required', 'document_required'];

    public function licenses()
    {
        return $this->belongsTo(Licenses::class, 'license_id', 'id');
    }
}
