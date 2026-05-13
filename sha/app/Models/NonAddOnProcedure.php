<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonAddOnProcedure extends Model
{
    protected $fillable = ['non_add_on_id','procedure_id'];

    public function procedure() {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    public function nonaddon_procedure() {
        return $this->belongsTo(Procedure::class, 'non_add_on_id');
    }
}
