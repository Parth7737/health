<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedure extends Model
{
    use SoftDeletes;
    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function scheme() {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }
    public function procedure_category() {
        return $this->belongsTo(ProcedureCategory::class, 'procedure_category_id');
    }
}
