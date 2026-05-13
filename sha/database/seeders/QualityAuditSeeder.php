<?php

namespace Database\Seeders;

use App\Models\{Hospitals, AuditList, AuditSubCategory, AuditCategory};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class QualityAuditSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        AuditCategory::truncate();
        AuditSubCategory::truncate();
        AuditList::truncate();

        $categories = ['Key Inputs', 'Clinical Services', 'Support Services', 'Patient Care', 'Health Outcomes'];

        foreach ($categories as $key => $value) {
            AuditCategory::create(['name' => $value]);
        }

        $subcategory = [
            [
                'category_id' => 1,
                'name' => "Physical Condition of the building and hospital environment shall be developed and maintained for the safetry of patients, visitors and staff",
                'list' => [
                    [
                        'name' => 'There should be no cattle or stay animals within the premises',
                        'is_required' => 1
                    ],
                    [
                        'name' => 'The facility should have a guard available 24*7',
                        'is_required' => 1
                    ],
                    [
                        'name' => 'The Hospital should be intact and not broken',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 1,
                'name' => "Hospital Should have adequate space for the ambulance and patient movement",
                'list' => [
                    [
                        'name' => 'Ambulance should have direct access to the emergency / receiving / triage area and access road to emergency should  be wide enough to streamline the movement of the patient till the emergency / receiving area',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'No vehicle should be parked on the way or in front of the emergency entrance',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 2,
                'name' => "Patients privac and confidentiality should be maintained at all times including Out Patient Department(OPD) and In-Patient Department(IPD)",
                'list' => [
                    [
                        'name' => 'Check availability for privacy screens or curtains in OPD and wards for maintaining visual provacy for the patients',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 2,
                'name' => "Patients privac and confidentiality should be maintained at all times including Out Patient Department(OPD) and In-Patient Department(IPD)",
                'list' => [
                    [
                        'name' => 'Check availability for privacy screens or curtains in OPD and wards for maintaining visual provacy for the patients',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 2,
                'name' => "The lab diagnostic services, whether in-house or outsourced, should be as per the scop of services.",
                'list' => [
                    [
                        'name' => 'List the number of in-house lab services',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'List the number outsourced lab services with their scopeof work.',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 3,
                'name' => "Hospital should be clean and have well-managed building. roofs floring and exterior",
                'list' => [
                    [
                        'name' => 'the floor should be non-slippery and dry',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'The floor surface should be smooth enough for effective cleaning and walking',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'The facility should be cleaned at least twice in the day with a wet mop and are also rigorously cleaned with scrubbing at least twice in a month. Check cleaning records',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 3,
                'name' => "Temperature control and ventilation should be maintained in patient care and nursing area",
                'list' => [
                    [
                        'name' => 'Availability of fans / air conditioning heating/ exhaust/ air vents as per the requirment and weather condition.',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 4,
                'name' => "All signage those are required by law should be displayed at all strategic location",
                'list' => [
                    [
                        'name' => 'Fire exit signage to be displayed at exit route plan along with the does and donts in case of fire',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'PC&PNDT Act signage board to be displayed at the waiting room and reception area',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'AERB and Radiation hazard Signage',
                        'is_required' => 0
                    ],
                ]
            ],
            [
                'category_id' => 4,
                'name' => "Contact information ok key medical staff and specialists should be readily available in the emergency department.",
                'list' => [
                    [
                        'name' => 'Check if the contact details (telephone or residence address) of doctors/staff are readily available.',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'Nurse call facility should be available to address any patient emergency',
                        'is_required' => 0
                    ], 
                ]
            ],
            [
                'category_id' => 5,
                'name' => "Monthly Out Patient Department(OPD) and In-Patient Department(IPD) Census.",
                'list' => [
                    [
                        'name' => 'Out Patient Department (OPD) census for last 6 months record available',
                        'is_required' => 0
                    ],
                    [
                        'name' => 'In-Patient Department (IPD) census for last 6 month record available',
                        'is_required' => 0
                    ], 
                ]
            ],
            [
                'category_id' => 5,
                'name' => "Mortality Rate and the Average Length of stay",
                'list' => [
                    [
                        'name' => 'Monthly Rate Recorded from the data of last 6 months(= Number of Patient died / Total number of patient admitted * 100) Average',
                        'is_required' => 0
                    ],
                ]
            ],
        ];

        foreach ($subcategory as $key => $value) {
            $data = AuditSubCategory::create(['name' => $value['name'], 'category_id' => $value['category_id']]);
            if(sizeof($value['list']) > 0) {
                foreach ($value['list'] as $k => $v) {
                    AuditList::create([
                        'category_id' => $value['category_id'],
                        'sub_category_id' => $data->id,
                        'name' => $v['name'],
                        'is_required' => $v['is_required'],
                    ]);
                }               
            }
        }

    }
}
