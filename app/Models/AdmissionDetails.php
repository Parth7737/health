<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionDetails extends Model
{
    protected $guarded = [];

    public function admission_type(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'admission_type_id');
    }
}
