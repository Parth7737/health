<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthDiagnosis extends Model
{
    public function diagnosis() {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }
}
