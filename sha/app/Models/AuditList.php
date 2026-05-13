<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditList extends Model
{
    protected $fillable = ['category_id', 'sub_category_id', 'name', 'is_required'];

    public function auditCategory() {
        return $this->belongsTo('App\Models\AuditCategory', 'category_id');
    }

    public function auditSubCategory() {
        return $this->belongsTo('App\Models\AuditSubCategory', 'sub_category_id');
    }
}
