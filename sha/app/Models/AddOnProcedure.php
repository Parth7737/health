<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOnProcedure extends Model
{
    protected $fillable = ['add_on_id','procedure_id'];
    public function procedure() {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    public function addon_procedure() {
        return $this->belongsTo(Procedure::class, 'add_on_id');
    }
}
