<?php

namespace App\Imports;

use App\Models\Procedure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProceduresImport implements ToModel, WithHeadingRow
{
    protected $speciality_id;
    protected $package_id;

    public function __construct($speciality_id, $package_id)
    {
        $this->speciality_id = $speciality_id;
        $this->package_id = $package_id;
    }

    public function model(array $row)
    {
        
        return new Procedure([
            'name' => $row[0],
            'package_id' => $this->package_id,
            'procedure_code_1' => $row[1] ?? null,
            'speciality_id' => $this->speciality_id,
            'procedure_code_2' => $row[2] ?? null,
            'is_multiple_procedure' => $row[3] ?? 0,
            'procedure_name' => $row[4] ?? null,
            'icd_code' => $row[5] ?? null,
            'price' => $row[6] ?? 0,
            'stratification_criteria' => $row[7] ?? null,
            'no_of_stratification' => $row[8] ?? null,
            'implants_high_end_consumables' => $row[9] ?? null,
            'more_than_one_implant' => $row[10] ?? null,
            'special_conditions' => $row[11] ?? null,
            'reservation_public_hospitals' => $row[12] ?? null,
            'reservation_tertiary_hospitals' => $row[13] ?? null,
            'level_of_care' => $row[14] ?? null,
            'los' => $row[15] ?? null,
            'auto_approved' => $row[16] ?? 0,
            'mandatory_documents_pre_auth' => $row[17] ?? null,
            'mandatory_documents_claim_processing' => $row[18] ?? null,
            'procedure_label' => $row[19] ?? null,
            'special_condition_pop_up' => $row[20] ?? null,
            'special_condition_pop_up_message' => $row[21] ?? null,
            'special_conditions_rule' => $row[22] ?? null,
            'special_conditions_rule_message' => $row[23] ?? null,
            'enhancement_applicable' => $row[24] ?? null,
            'medical_or_surgical' => $row[25] ?? null,
            'day_care_procedure' => $row[26] ?? null,
            'status' => 1,
        ]);
    }
}
