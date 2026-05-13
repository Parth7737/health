<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{
    
    Speciality,
    Package,
    Investigation,
    StratificationCategory,
    Stratification,
    FollowupProcedure,
    AddOnProcedure,
    NonAddOnProcedure,
    Procedure,
    HospitalSpeciality,
    HospitalTeam,
};

class HospitalSpecialitiesSeeder extends Seeder
{
    public function run()
    {
        
        Speciality::truncate();
        Package::truncate();
        Investigation::truncate();
        StratificationCategory::truncate();
        Stratification::truncate();
        FollowupProcedure::truncate();
        AddOnProcedure::truncate();
        NonAddOnProcedure::truncate();
        Procedure::truncate();
        HospitalSpeciality::truncate();
        HospitalTeam::truncate();
        $investigations = [
            ['code' => 'MAND0003', 'name' => 'Clinical Notes'],
            ['code' => 'MAND0369', 'name' => 'MLC copy with number'],
            ['code' => 'MAND0370', 'name' => 'Extent of burns visible on photograph (with rule of 9 chart)'],
            ['code' => 'MAND0379', 'name' => 'Clinical notes with planned line of treatment'],
            ['code' => 'MAND0339', 'name' => 'Pre-clinical photograph'],
        ];

        foreach ($investigations as $investigation) {
            Investigation::create($investigation);
        }

        $stratifications = [
            ['name' => 'Anesthesia'],
        ];

        foreach ($stratifications as $stratification) {
            StratificationCategory::create($stratification);
        }

        $stratifications = [
            ['stratification_category_id' => '1', 'name' => 'Local Anesthesia','code'=>'STRAT001','code2'=>'STRAT001a','rule'=>'a','price'=>'0','procedure_id'=>'4'],
            ['stratification_category_id' => '1', 'name' => 'General Anesthesia','code'=>'STRAT001','code2'=>'STRAT001b','rule'=>'b','price'=>'5000','procedure_id'=>'4'],
        ];

        foreach ($stratifications as $stratification) {
            Stratification::create($stratification);
        }

        $follow_up_procedures = [
            ['procedure_id' => '4', 'follow_up_id' => '5'],
        ];

        foreach ($follow_up_procedures as $follow_up_procedure) {
            FollowupProcedure::create($follow_up_procedure);
        }

        $add_on_procedures = [
            ['procedure_id' => '6', 'add_on_id' => '3'],
        ];

        foreach ($add_on_procedures as $add_on_procedure) {
            AddOnProcedure::create($add_on_procedure);
        }

        $non_add_on_procedures = [
            ['procedure_id' => '7', 'non_add_on_id' => '8'],
        ];

        foreach ($non_add_on_procedures as $non_add_on_procedure) {
            NonAddOnProcedure::create($non_add_on_procedure);
        }
        $speciality = [
            ['code' => 'BM', 'name' => 'Burns Management'],
            ['code' => 'ER', 'name' => 'Emergency Room Packages'],
            ['code' => 'MR', 'name' => 'Radiation Oncology'],
            ['code' => 'SB', 'name' => 'Orthopedics'],
            ['code' => 'MG', 'name' => 'General Medicine'],
        ];

        foreach ($speciality as $spec) {
            Speciality::create($spec);
        }
        
        $packages = [
            ['code' => 'BM001', 'name' => 'Thermal burns'],
            ['code' => 'ER001', 'name' => 'Laceration - Suturing / Dressing'],
            ['code' => 'MR003', 'name' => '2D External Beam Radiotherapy (25 Fractions) (Inclusive of Simulation & Planning Cost)'],
            ['code' => 'SB006', 'name' => 'Percutaneous - Fixation of Fracture'],
            ['code' => 'SB070', 'name' => 'Implant Removal under LA'],
            ['code' => 'MG072', 'name' => 'Haemodialysis / Peritoneal Dialysis'],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
        DB::table('hospital_specialities')->insert([
            [
                'hospital_id' => 0,
                'uuid' => 'fcef45fd-c840-2335-ttg5-28dfeb1ee3e8',
                'speciality_id' => 1,
                'available' => 1,
                'offered' => 1,
                'not_offered_reason' => null,
                'remark' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'hospital_id' => 0,
                'uuid' => 'fcef45fd-c840-2335-ttg5-28dfeb1ee3e8',
                'speciality_id' => 2,
                'available' => 1,
                'offered' => 1,
                'not_offered_reason' => null,
                'remark' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'hospital_id' => 0,
                'uuid' => 'fcef45fd-c840-2335-ttg5-28dfeb1ee3e8',
                'speciality_id' => 3,
                'available' => 1,
                'offered' => 1,
                'not_offered_reason' => null,
                'remark' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'hospital_id' => 0,
                'uuid' => 'fcef45fd-c840-2335-ttg5-28dfeb1ee3e8',
                'speciality_id' => 4,
                'available' => 1,
                'offered' => 1,
                'not_offered_reason' => null,
                'remark' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'hospital_id' => 0,
                'uuid' => 'fcef45fd-c840-2335-ttg5-28dfeb1ee3e8',
                'speciality_id' => 5,
                'available' => 1,
                'offered' => 1,
                'not_offered_reason' => null,
                'remark' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

        // Insert data into `procedures` table
        DB::table('procedures')->insert([
            [
                'id' => 1,
                'name' => null,
                'package_id' => 1,
                'procedure_code_1' => 'S1100001',
                'speciality_id' => 1,
                'procedure_code_2' => 'BM001A',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => "% Total Body Surface Area Burns (TBSA) - any % \n(not requiring admission). \nNeeds at least 5-6 dressing",
                'icd_code' => 'N/A',
                'price' => 4445.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 3,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'No',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Tertiary',
                'los' => 'NA',
                'auto_approved' => 'No',
                'mandatory_documents_pre_auth' => '1,2,3',
                'mandatory_documents_claim_processing' => '1,2,3',
                'procedure_label' => 'Regular Procedure',
                'special_condition_pop_up' => 'No',
                'special_condition_pop_up_message' => '',
                'special_conditions_rule' => 'No',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Surgical',
                'day_care_procedure' => 'No',
                'status' => 1,
                'created_at' => '2024-12-11 22:13:02',
                'updated_at' => '2024-12-11 22:57:48',
            ],
            [
                'id' => 2,
                'name' => null,
                'package_id' => 2,
                'procedure_code_1' => 'New Package',
                'speciality_id' => 2,
                'procedure_code_2' => 'ER001A',
                'is_multiple_procedure' => 'No',
                'procedure_name' => 'Laceration - Suturing / Dressing',
                'icd_code' => 'LSD',
                'price' => 2000.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 0,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'No',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Secondary',
                'los' => 'NA',
                'auto_approved' => 'Yes',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Regular Procedure',
                'special_condition_pop_up' => 'No',
                'special_condition_pop_up_message' => '',
                'special_conditions_rule' => 'No',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Surgical',
                'day_care_procedure' => 'No',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'name' => null,
                'package_id' => 3,
                'procedure_code_1' => 'New Package',
                'speciality_id' => 3,
                'procedure_code_2' => 'MR003D',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => 'Additional fractions - 2',
                'icd_code' => '',
                'price' => 0.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 0,
                'implants_high_end_consumables' => 'Yes',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'Yes',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Tertiary',
                'los' => '1',
                'auto_approved' => 'NO',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Add-on Procedure',
                'special_condition_pop_up' => 'Yes',
                'special_condition_pop_up_message' => 'Can be booked only after MR003A / MR003B / MR003C',
                'special_conditions_rule' => 'Yes',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Medical',
                'day_care_procedure' => 'Yes',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 4,
                'name' => null,
                'package_id' => 4,
                'procedure_code_1' => 'S500073',
                'speciality_id' => 4,
                'procedure_code_2' => 'SB006A',
                'is_multiple_procedure' => 'No',
                'procedure_name' => 'Percutaneous - Fixation of Fracture',
                'icd_code' => '',
                'price' => 3000.00,
                'stratification_criteria' => 'Yes',
                'no_of_stratification' => 1,
                'implants_high_end_consumables' => 'Yes',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'No',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Secondary',
                'los' => '4',
                'auto_approved' => 'Yes',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Regular Procedure',
                'special_condition_pop_up' => 'No',
                'special_condition_pop_up_message' => '',
                'special_conditions_rule' => 'No',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Surgical',
                'day_care_procedure' => 'No',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 5,
                'name' => null,
                'package_id' => 5,
                'procedure_code_1' => 'New Package',
                'speciality_id' => 4,
                'procedure_code_2' => 'SB070A',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => 'K - Wire',
                'icd_code' => '',
                'price' => 5000.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 1,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'Yes',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Secondary',
                'los' => '1',
                'auto_approved' => 'No',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Follow-up Procedure',
                'special_condition_pop_up' => 'Yes',
                'special_condition_pop_up_message' => 'LA for - K Wire & Screw GA for - Nail & Plate',
                'special_conditions_rule' => 'No',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Surgical',
                'day_care_procedure' => 'Yes',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 6,
                'name' => null,
                'package_id' => 3,
                'procedure_code_1' => 'New Package',
                'speciality_id' => 3,
                'procedure_code_2' => 'MR003A',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => 'Radical',
                'icd_code' => '',
                'price' => 20000.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 0,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'No',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Tertiary',
                'los' => '1',
                'auto_approved' => 'No',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Regular Procedure',
                'special_condition_pop_up' => 'No',
                'special_condition_pop_up_message' => '',
                'special_conditions_rule' => 'No',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Medical',
                'day_care_procedure' => 'Yes',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 7,
                'name' => null,
                'package_id' => 6,
                'procedure_code_1' => 'M100070',
                'speciality_id' => 5,
                'procedure_code_2' => 'MG072C',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => 'Acute Haemodialysis',
                'icd_code' => '',
                'price' => 1500.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 0,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'Yes',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Secondary',
                'los' => '1',
                'auto_approved' => 'No',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Add-on Procedure',
                'special_condition_pop_up' => 'Yes',
                'special_condition_pop_up_message' => 'Package cost of one session',
                'special_conditions_rule' => 'Yes',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Medical',
                'day_care_procedure' => 'Yes',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
            [
                'id' => 8,
                'name' => null,
                'package_id' => 6,
                'procedure_code_1' => 'M100070',
                'speciality_id' => 5,
                'procedure_code_2' => 'MG072B',
                'is_multiple_procedure' => 'Yes',
                'procedure_name' => 'Peritoneal Dialysis',
                'icd_code' => '',
                'price' => 1500.00,
                'stratification_criteria' => 'No',
                'no_of_stratification' => 0,
                'implants_high_end_consumables' => 'No',
                'more_than_one_implant' => 'No',
                'special_conditions' => 'Yes',
                'reservation_public_hospitals' => 'No',
                'level_of_care' => 'Secondary',
                'los' => '1',
                'auto_approved' => 'No',
                'mandatory_documents_pre_auth' => '4,5',
                'mandatory_documents_claim_processing' => '4,5',
                'procedure_label' => 'Add-on Procedure',
                'special_condition_pop_up' => 'Yes',
                'special_condition_pop_up_message' => 'Package cost of one session',
                'special_conditions_rule' => 'Yes',
                'special_conditions_rule_message' => '',
                'enhancement_applicable' => 'No',
                'medical_or_surgical' => 'Medical',
                'day_care_procedure' => 'Yes',
                'status' => 1,
                'created_at' => '2025-01-01 19:07:48',
                'updated_at' => null,
            ],
        ]);

        // Insert data into `hospital_teams` table
        DB::table('hospital_teams')->insert([
            [
                'uuid' => '4d77c455-c85f-11ef-97df-28dfeb1ee3e8',
                'hospital_id' => 0,
                'speciality_id' => 1,
                'name' => 'Harsh Shah',
                'hpr_id' => '6745747756',
                'designation' => 'Doctor',
                'employement_type' => 'Visiting Consultant',
                'registration_no' => '4545',
                'email' => 'harsh@demo.com',
                'mobile' => '8756999867',
                'registration_certificate' => null,
                'declaration_certificate' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'uuid' => '4d77c455-c85f-11ef-97df-28dfeb1ee3e8',
                'hospital_id' => 0,
                'speciality_id' => 2,
                'name' => 'Kairavi Busa',
                'hpr_id' => '564376875767',
                'designation' => 'Doctor',
                'employement_type' => 'FullTime Consultant',
                'registration_no' => '8787',
                'email' => 'kairavi@demo.com',
                'mobile' => '9898567678',
                'registration_certificate' => null,
                'declaration_certificate' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'uuid' => '4d77c455-c85f-11ef-97df-28dfeb1ee3e8',
                'hospital_id' => 0,
                'speciality_id' => 3,
                'name' => 'Manan Patel',
                'hpr_id' => '564376875767',
                'designation' => 'Doctor',
                'employement_type' => 'FullTime Consultant',
                'registration_no' => '87557',
                'email' => 'manan@demo.com',
                'mobile' => '9898567678',
                'registration_certificate' => null,
                'declaration_certificate' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'uuid' => '4d77c455-c85f-11ef-97df-28dfeb1ee3e8',
                'hospital_id' => 0,
                'speciality_id' => 4,
                'name' => 'Krishna Rangani',
                'hpr_id' => '657646464556',
                'designation' => 'Doctor',
                'employement_type' => 'FullTime Consultant',
                'registration_no' => '909067',
                'email' => 'krishna@demo.com',
                'mobile' => '9898567678',
                'registration_certificate' => null,
                'declaration_certificate' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
