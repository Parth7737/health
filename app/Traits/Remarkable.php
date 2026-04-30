<?php

namespace App\Traits;

use App\Models\PreauthRemark;

trait Remarkable
{
    public function preauthRemark()
    {
        return $this->morphMany(PreauthRemark::class, 'remarkable');
    }
}
