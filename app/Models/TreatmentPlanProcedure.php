<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentPlanProcedure extends Model
{
    use SoftDeletes;

    protected $table = 'procedures';

    protected $fillable = [
        'scheme_type_id',
        'name',
        'package_id',
        'procedure_category_id',
        'speciality_id',
        'procedure_code_1',
        'procedure_code_2',
        'is_multiple_procedure',
        'procedure_name',
        'icd_code',
        'procedure_type',
        'price',
        'non_nabh_price',
        'stratification_criteria',
        'no_of_stratification',
        'implants_high_end_consumables',
        'more_than_one_implant',
        'special_conditions',
        'reservation_public_hospitals',
        'reservation_tertiary_hospitals',
        'level_of_care',
        'los',
        'auto_approved',
        'mandatory_documents_pre_auth',
        'mandatory_documents_claim_processing',
        'procedure_label',
        'special_condition_pop_up',
        'special_condition_pop_up_message',
        'special_conditions_rule',
        'special_conditions_rule_message',
        'enhancement_applicable',
        'medical_or_surgical',
        'day_care_procedure',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'non_nabh_price' => 'decimal:2',
        'scheme_type_id' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanPackage::class, 'package_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedureCategory::class, 'procedure_category_id');
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function schemeType(): BelongsTo
    {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }

    public function implants(): HasMany
    {
        return $this->hasMany(TreatmentPlanImplant::class, 'procedure_id');
    }
}
