<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyTestParameter extends Model
{
    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(RadiologyTest::class, 'radiology_test_id');
    }

    public function parameter()
    {
        return $this->belongsTo(RadiologyParameter::class, 'radiology_parameter_id');
    }
}
