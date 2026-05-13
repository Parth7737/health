<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthRemark extends Model
{
    protected $fillable = ['content', 'content_id', 'added_by', 'type'];

    public function remarkable()
    {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
