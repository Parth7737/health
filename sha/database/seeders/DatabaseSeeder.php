<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{ 
    SchemeType,
    HospitalType,
    HospitalSubType,
    Role,
    EmpanelmentType,
    Benificiary,
    Diabetes,
    Hypertension,
    HeartDisease,
    Stroke,
    Cancer,
    Tuberculosis,
    Asthma,
    Appetite,
    Bowels,
    Nutrition,
    Diet,
    AdmissionType,
    Diagnosis,
    Accreditation,
    TdsExemption,
    EmpanelmentDocument,
    HospitalState,
    HospitalDistrict,
    RegistrationCancelReason,
    PreauthCancelReason,
    PreauthRejectReason,
    PreauthClaimReason
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();
        // User::factory(10)->create();
        User::where('email', 'admin@sha.com')->delete();
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sha.com',
            'password' => \Hash::make(123456),
            'role_id' => 1,
        ]);
        User::factory()->create([
            'name' => 'Jignesh Patel',
            'email' => 'jigneshkakdiya3998@gmail.com',
            'userid' => 'jignesh01',
            'password' => \Hash::make(123456),
            'mobile_no' => 9999985698,
            'role_id' => 2,
        ]);
        User::factory()->create([
            'name' => 'Parth Patel',
            'hospital_id' => 0,
            'email' => 'parthdholariya7738@gmail.com',
            'userid' => 'parth01',
            'password' => \Hash::make(123456),
            'role_id' => 4,
        ]);

        User::factory()->create([
            'name' => 'Parag Patel',
            'email' => 'paragpatel@gmail.com',
            'userid' => 'paragpatel01',
            'password' => \Hash::make(123456),
            'mobile_no' => 8888899999,
            'role_id' => 6,
            'district' => 91,
        ]);

        User::factory()->create([
            'name' => 'Swati Patel',
            'email' => 'swatipatel@gmail.com',
            'userid' => 'swatipatel01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7878858589,
            'role_id' => 7,
            'district' => 91,
        ]);
        User::factory()->create([
            'name' => 'SEC Admin',
            'email' => 'sec@gmail.com',
            'userid' => 'secadmin',
            'password' => \Hash::make(123456),
            'mobile_no' => 9698969596,
            'role_id' => 8,
            'district' => 91,
        ]);
        User::factory()->create([
            'name' => 'Yagnik Patel',
            'email' => 'yagnik01@gmail.com',
            'userid' => 'yagnik01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7878858589,
            'role_id' => 13,
        ]);
        User::factory()->create([
            'name' => 'Sanjay Maheta',
            'email' => 'sanjay01@gmail.com',
            'userid' => 'sanjay01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7878858589,
            'role_id' => 14,
        ]);
        User::factory()->create([
            'name' => 'CEX Admin',
            'email' => 'cexadmin01@gmail.com',
            'userid' => 'cexadmin01',
            'password' => \Hash::make(123456),
            'mobile_no' => 6565696998,
            'role_id' => 15,
        ]);
        User::factory()->create([
            'name' => 'ACO Admin',
            'email' => 'acoadmin01@gmail.com',
            'userid' => 'acoadmin01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7089555668,
            'role_id' => 16,
        ]);
        User::factory()->create([
            'name' => 'SHA Admin',
            'email' => 'shaadmin01@gmail.com',
            'userid' => 'shaadmin01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7036555668,
            'role_id' => 17,
        ]);

        User::factory()->create([
            'name' => 'SHA Super Admin',
            'email' => 'shaadmin@gmail.com',
            'mobile_no' => '9985699778',
            'password' => \Hash::make(123456),
            'role_id' => 18,
            'state' => 'Uttarakhand',
            'district' => 91,
            "parent_entity" => "Uttarakhand",
            "entity_type" => "SHA",
            "entity_name" => "SHA Entity"
        ]);
        User::factory()->create([
            'name' => 'Medical Committee',
            'email' => 'shaadmin01@gmail.com',
            'userid' => 'shaadmin01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7036555668,
            'role_id' => 20,
        ]);
        User::factory()->create([
            'name' => 'CEO',
            'email' => 'ceo01@gmail.com',
            'userid' => 'ceo01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7036555668,
            'role_id' => 21,
        ]);
        User::factory()->create([
            'name' => 'ACS/Chairman',
            'email' => 'acs@gmail.com',
            'userid' => 'acs01',
            'password' => \Hash::make(123456),
            'mobile_no' => 7036555668,
            'role_id' => 22,
        ]);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        SchemeType::truncate();
        Benificiary::truncate();
        HospitalType::truncate();
        HospitalSubType::truncate();
        EmpanelmentType::truncate();
        Diabetes::truncate();
        Hypertension::truncate();
        HeartDisease::truncate();
        Stroke::truncate();
        Cancer::truncate();
        Tuberculosis::truncate();
        Asthma::truncate();
        Appetite::truncate();
        Bowels::truncate();
        Nutrition::truncate();
        Diet::truncate();
        AdmissionType::truncate();
        Diagnosis::truncate();
        Role::truncate();
        Accreditation::truncate();
        TdsExemption::truncate();
        EmpanelmentDocument::truncate();
        RegistrationCancelReason::truncate();
        PreauthCancelReason::truncate();
        PreauthRejectReason::truncate();
        PreauthClaimReason::truncate();

        SchemeType::create(['name' => 'SGHS/AAUY']);

        Diabetes::create(['name' => 'Gestational diabetes']);
        Hypertension::create(['name' => 'Primary hypertension']);
        HeartDisease::create(['name' => 'Cardiovascular']);
        Stroke::create(['name' => 'Ischemic']);
        Cancer::create(['name' => 'Carcinoma']);
        Tuberculosis::create(['name' => 'Consumption']);
        Asthma::create(['name' => 'Allergic Asthma']);
        Appetite::create(['name' => 'Physical']);
        Bowels::create(['name' => 'Small Intestine']);
        Nutrition::create(['name' => 'Nutrients']);
        Diet::create(['name' => 'Vegan Diet']);
        AdmissionType::create(['name' => 'Plan']);
        RegistrationCancelReason::create(['name' => 'Benificiary register by mistake']);
        PreauthCancelReason::create(['name' => 'Preauth by mistake']);
        PreauthCancelReason::create(['name' => 'Other']);
        PreauthRejectReason::create(['name' => 'Not Correct Documents']);
        Diagnosis::create(['name' => 'MRI Scan','code' => 'MS']);
        Diagnosis::create(['name' => 'Clinical Diagnosis','code' => 'CD']);

        PreauthClaimReason::create(['name' => 'Invalid Data Approved by PPD', 'type' => 'Reject']);
        PreauthClaimReason::create(['name' => 'Document Not Approved', 'type' => 'Reject']);
        PreauthClaimReason::create(['name' => 'Admission Information is missing', 'type' => 'Query']);
        PreauthClaimReason::create(['name' => 'Finance Details Check Again', 'type' => 'Query']);

        Accreditation::create(['name' => 'SGHS Bronze Certification','percentage'=>15]);
        Accreditation::create(['name' => 'SGHS Gold Certification','percentage'=>18]);
        Accreditation::create(['name' => 'SGHS Sliver Certification','percentage'=>15]);
        Accreditation::create(['name' => 'NABH Accreditation','percentage'=>18]);
        Accreditation::create(['name' => 'NABH Entry Certificate','percentage'=>15]);
        
        TdsExemption::create(['name' => '17(2) B Form']);
        TdsExemption::create(['name' => 'Tax Exemption Certificate - if any']);
        TdsExemption::create(['name' => 'TDS Exemption Certificate']);

        // EmpanelmentDocument::create(['name' => 'Hospital Profile PPT','is_required'=>1]);
        // EmpanelmentDocument::create(['name' => 'Pan Card Copy','is_required'=>1]);
        EmpanelmentDocument::create(['name' => 'Legal Entity Certificate in Case of PAN Card Issued on Individual Name','is_required'=>1]);
        // EmpanelmentDocument::create(['name' => 'Service Tax No. Certificate','is_required'=>1]);
        EmpanelmentDocument::create(['name' => 'Hospital Registration Certificate','is_required'=>1]);

        Benificiary::create([
            'name' => 'Kishan Pali',
            'scheme_id' => '1',
            'card_id' => 'FGD56D34D',
            'aabha_id' => '56-4545-5608-3408',
            'mobile_no' => '9998568573',
            'gender' => 'Male',
            'age' => '26',
            'state' => '1',
            'district' => '2',
            'city' => 'Haryana',
            'pincode' => '123102',
            'address' => '204,Banjara basti nagar,Sonipat road',
            'care_plan' => 'SGSH/HR/S',
            'image' => 'kishan.png',
        ]);

        $types = [
            ['name' => 'Public', 'display_name' => 'Public'],
            ['name' => 'PrivateNP', 'display_name' => 'Private(Not For Profit)'],
            ['name' => 'PrivateP', 'display_name' => 'Private(For Profit)'],
            ['name' => 'Temporary', 'display_name' => 'Temporary Empanelment - HEM Lite'],
            ['name' => 'labEmpanelment', 'display_name' => 'Lab Empanelment'],
            ['name' => 'GOI', 'display_name' => 'Government Of India']
        ];

        foreach ($types as $type) {
            HospitalType::create($type);
        }

        $subTypes = [
            ['code' => 'CHC', 'name' => 'CHC', 'hospital_type_id' => 1],
            ['code' => 'PHC', 'name' => 'PHC', 'hospital_type_id' => 1],
            ['code' => 'MCH', 'name' => 'Medical College Hospital', 'hospital_type_id' => 1],
            ['code' => 'SDH', 'name' => 'Sub-District Hospital', 'hospital_type_id' => 1],
            ['code' => 'DH', 'name' => 'District Hospital', 'hospital_type_id' => 1]
        ];

        foreach ($subTypes as $subType) {
            HospitalSubType::create($subType);
        }
        
        

        $empanelmentTypes = [
            ['name' => 'SGHS'],
            ['name' => 'Only CGHS'],
            ['name' => 'State Specific Empanelment'],
            ['name' => 'CMAAY']
        ];

        foreach ($empanelmentTypes as $empanelmentType) {
            EmpanelmentType::create($empanelmentType);
        }

        $roles = [
            [
                'name' => "Super Admin",
                'entity' => ''
            ], 
            [
                'name' => "Admin",
                'entity' => 'Hospital Onboarding Entity'
            ],
            [
                'name' => 'Hospital Administrator',
                'entity' => ''
            ],
            [
                'name' => 'Medical Superintendent',
                'entity' => ''
            ],
            [
                'name' => 'Medical Coordinator',
                'entity' => ''
            ],
            [
                'name' => "DEC Officer",
                'entity' => 'District Empanelment Committee'
            ],
            [
                'name' => "DEC Physical Verifier",
                'entity' => 'District Empanelment Committee'
            ],
            [
                'name' => "SEC Officer",
                'entity' => 'State Empanelment Committee'
            ],
            [
                'name' => "SEC Physical Verifier",
                'entity' => 'State Empanelment Committee'
            ],
            [
                'name' => "Pathology Lab Admin",
                'entity' => 'Pathology Lab Onboarding Entity'
            ],
            [
                'name' => "Pharmacy Admin",
                'entity' => 'Pharmacy Onboarding Entity'
            ],
            [
                'name' => "Radiology Lab Admin",
                'entity' => 'Radiology Lab Hospital Onboarding Entity'
            ],
            [
                'name' => "PPD",
                'entity' => 'ISA Entity'
            ],
            [
                'name' => "CPD",
                'entity' => 'ISA Entity'
            ],
            [
                'name' => "CEX",
                'entity' => 'ISA Entity'
            ],
            [
                'name' => "ACO",
                'entity' => 'ISA Entity'
            ],
            [
                'name' => "SHA",
                'entity' => 'SHA Entity'
            ],
            [
                'name' => "SHA Admin",
                'entity' => ''
            ],
            [
                'name' => "ISA Admin",
                'entity' => 'SHA Entity'
            ],
            [
                'name' => "Medical Committee",
                'entity' => 'SHA Entity'
            ],
            [
                'name' => "CEO",
                'entity' => 'SHA Entity'
            ],
            [
                'name' => "ACS/Chairman",
                'entity' => 'SHA Entity'
            ],
        ];

        foreach ($roles as $role) {
            Role::create([
               'name' => $role['name'],      // Use the name from the current role
                'entity' => $role['entity'], 
            ]);
        }
        
        $this->call([
            SettingSeeder::class,
            EmpanelMentSeeder::class,
            HospitalSpecialitiesSeeder::class,
            ServiceSeeder::class,
            LicensesSeeder::class,
            HumanResourceSeeder::class,
            StateDistrictSeeder::class
        ]);
    }
}
