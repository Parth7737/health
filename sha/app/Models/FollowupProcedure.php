<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowupProcedure extends Model
{
    protected $fillable = ['follow_up_id','procedure_id'];

    public function procedure() {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    public function follow_procedure() {
        return $this->belongsTo(Procedure::class, 'follow_up_id');
    }
}
