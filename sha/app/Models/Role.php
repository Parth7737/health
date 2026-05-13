<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'is_custom',
        'entity'
    ];
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }
}
