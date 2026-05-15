<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreauthDiagnosis extends Model
{
    protected $guarded = [];

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(PreauthDiagnosisMaster::class, 'diagnosis_id');
    }
}
