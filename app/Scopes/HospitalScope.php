<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\CentralLogics\Helpers;

class HospitalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // only add the constraint when we have a hospital id available
        if ($hospitalId = Helpers::getHospitalId()) {
            $builder->where($model->getTable() . '.hospital_id', $hospitalId);
        }
    }
}
