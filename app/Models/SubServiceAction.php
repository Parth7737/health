<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubServiceAction extends Model
{
    protected $fillable = ['sub_service_id', 'type', 'label', 'value', 'is_text_input', 'sublabel', 'is_image', 'bed_count'];
}
