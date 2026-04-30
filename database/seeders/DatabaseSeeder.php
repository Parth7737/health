<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{ 
    Role,
    HospitalType
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::truncate();
        // Role::truncate();

        $user = User::firstOrCreate(
            ['email' => 'admin@paracare.com'],
            [
            'name' => 'Super Admin',
            'email' => 'admin@paracare.com',
            'password' => \Hash::make(123456),
        ]);

        $roles = [
            [
                'name' => "Master Admin",
                'guard_name' => 'web',
                'is_custom' => 0,
            ], 
            [
                'name' => "Administrator",
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Doctor',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Nurse',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Pharmacist',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Lab Technician',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Radiologist',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Billing Staff',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Ambulance Driver',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'Bloodbank',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'HR',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
            [
                'name' => 'State Super Admin',
                'guard_name' => 'web',
                'is_custom' => 0,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                [
                    'name' => $role['name'],
                    'is_custom' => $role['is_custom'], 
                    'guard_name' => $role['guard_name'],
                ]
            );
        }
        $user->assignRole('Master Admin');
        $hospital_types = ['State Government Hospital/Medical Collage','Central Government Hospital/Medical Collage','Private Hospital/Medical Collage'];
        foreach ($hospital_types as $hospital_type) {
            HospitalType::firstOrCreate(
                ['name' => $hospital_type],
                [
                'name' => $hospital_type,
            ]);
        }
        $this->call([
            SettingSeeder::class,
            HospitalPermissionsSeeder::class,
            CountryCodeSeeder::class,
            NationalitySeeder::class,
        ]);
    }
}
