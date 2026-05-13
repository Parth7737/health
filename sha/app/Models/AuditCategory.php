<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditCategory extends Model
{
    protected $fillable = ['name'];

    public function auditSubCategories() {
        return $this->hasMany('App\Models\AuditSubCategory', 'category_id');
    }

    public function auditlist() {
        return $this->hasMany('App\Models\AuditList', 'category_id');
    }
}
