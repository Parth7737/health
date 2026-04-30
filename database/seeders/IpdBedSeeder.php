<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\BedStatus;
use App\Models\Hospital;
use App\Models\Floor;
use Illuminate\Database\Seeder;

class IpdBedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first hospital
        $hospital = Hospital::first();
        if (!$hospital) {
            return;
        }

        // Create Building
        $building = Building::firstOrCreate(
            [
                'hospital_id' => $hospital->id,
                'building_code' => 'BLD-001',
            ],
            [
                'building_name' => 'Main Building',
                'floors_count' => 3,
                'description' => 'Main Hospital Building',
            ]
        );

        $bedTypes = [
            ['type_name' => 'General', 'description' => 'General Bed', 'base_charge' => 500],
            ['type_name' => 'Semi-Private', 'description' => 'Semi-Private Bed', 'base_charge' => 1000],
            ['type_name' => 'Private', 'description' => 'Private Bed', 'base_charge' => 1500],
            ['type_name' => 'ICU', 'description' => 'ICU Bed', 'base_charge' => 3000],
        ];

        $bedTypeMap = collect($bedTypes)->mapWithKeys(function ($type) use ($hospital) {
            $bedType = BedType::firstOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'type_name' => $type['type_name'],
                ],
                [
                    'description' => $type['description'],
                    'base_charge' => $type['base_charge'],
                    'is_active' => true,
                ]
            );

            return [$type['type_name'] => $bedType];
        });

        // Get or create floor
        $floor = Floor::where('hospital_id', $hospital->id)->first();
        if (!$floor) {
            $floor = Floor::create([
                'hospital_id' => $hospital->id,
                'building_id' => $building->id,
                'name' => 'Ground Floor',
            ]);
        }

        // Create Wards
        $wardTypes = [
            ['ward_code' => 'GEN-001', 'ward_name' => 'General Ward', 'ward_type' => 'General', 'beds' => 20],
            ['ward_code' => 'ICU-001', 'ward_name' => 'ICU Ward', 'ward_type' => 'ICU', 'beds' => 8],
            ['ward_code' => 'ORT-001', 'ward_name' => 'Orthopedic Ward', 'ward_type' => 'Orthopedic', 'beds' => 12],
        ];

        foreach ($wardTypes as $wardData) {
            $ward = Ward::firstOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'ward_code' => $wardData['ward_code'],
                ],
                [
                    'floor_id' => $floor->id,
                    'ward_name' => $wardData['ward_name'],
                    'total_beds' => $wardData['beds'],
                    'description' => "{$wardData['ward_name']} - {$wardData['ward_type']} Beds",
                    'is_active' => true,
                ]
            );

            // Create rooms and beds for each ward
            $bedsPerRoom = $wardData['ward_type'] == 'ICU' ? 2 : 4;
            $numRooms = ceil($wardData['beds'] / $bedsPerRoom);

            for ($roomNum = 1; $roomNum <= $numRooms; $roomNum++) {
                $roomCode = strtoupper(substr($wardData['ward_type'], 0, 3)) . '-' . str_pad($roomNum, 3, '0', STR_PAD_LEFT);
                
                $room = Room::firstOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'room_code' => $roomCode,
                    ],
                    [
                        'ward_id' => $ward->id,
                        'room_number' => "Room {$roomNum}",
                        'bed_capacity' => $bedsPerRoom,
                        'notes' => "{$wardData['ward_name']} - Room {$roomNum}",
                        'is_active' => true,
                    ]
                );

                // Create beds
                $bedTypeId = $wardData['ward_type'] == 'ICU'
                    ? $bedTypeMap['ICU']->id
                    : $bedTypeMap[collect(['General', 'Semi-Private', 'Private'])->random()]->id;
                
                for ($bedNum = 1; $bedNum <= $bedsPerRoom; $bedNum++) {
                    $bedCode = "BED-" . str_pad($roomNum, 3, '0', STR_PAD_LEFT) . "-" . str_pad($bedNum, 2, '0', STR_PAD_LEFT);
                    
                    Bed::firstOrCreate(
                        [
                            'hospital_id' => $hospital->id,
                            'bed_code' => $bedCode,
                        ],
                        [
                            'room_id' => $room->id,
                            'bed_type_id' => $bedTypeId,
                            'bed_status_id' => BedStatus::AVAILABLE,
                            'bed_number' => $bedNum,
                            'notes' => "Bed {$bedNum}",
                        ]
                    );
                }
            }
        }

        $this->command->info('IPD Bed System seeded successfully!');
    }
}
