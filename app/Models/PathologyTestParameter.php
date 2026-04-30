<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathologyTestParameter extends Model
{
    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(PathologyTest::class, 'pathology_test_id');
    }

    public function parameter()
    {
        return $this->belongsTo(PathologyParameter::class, 'pathology_parameter_id');
    }
}
