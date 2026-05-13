<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditSubCategory extends Model
{
    protected $fillable = ['category_id', 'name'];

    public function auditCategory() {
        return $this->belongsTo('App\Models\AuditCategory', 'category_id');
    }

    public function auditlist() {
        return $this->hasMany('App\Models\AuditList', 'sub_category_id');
    }
}
