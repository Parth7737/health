<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BeneficiaryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Beneficiary([
            'name' => $row['name'] ?? null,
            'father_name' => $row['father_name'] ?? null,
            'scheme_id' => $row['scheme_id'] ?? null,
            'card_id' => $row['card_id'] ?? null,
            'care_plan' => $row['care_plan'] ?? null,
            'aabha_id' => $row['aabha_id'] ?? null,
            'mobile_no' => $row['mobile_no'] ?? null,
            'gender' => $row['gender'] ?? null,
            'year_of_birth' => $row['year_of_birth'] ?? null,
            'age' => $row['age'] ?? null,
            'state' => $row['state'] ?? null,
            'state_name' => $row['state_name'] ?? null,
            'state_cd' => $row['state_cd'] ?? null,
            'district' => $row['district'] ?? null,
            'dist_name' => $row['dist_name'] ?? null,
            'dist_cd' => $row['dist_cd'] ?? null,
            'city' => $row['city'] ?? null,
            'pincode' => $row['pincode'] ?? null,
            'address' => $row['address'] ?? null,
            'ben_id' => $row['ben_id'] ?? null,
            'ben_mobile_no' => $row['ben_mobile_no'] ?? null,
            'ben_email_id' => $row['ben_email_id'] ?? null,
            'active_status' => $row['active_status'] ?? null,
            'enrl_status' => $row['enrl_status'] ?? null,
        ]);
    }
}
