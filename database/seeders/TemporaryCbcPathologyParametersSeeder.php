<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemporaryCbcPathologyParametersSeeder extends Seeder
{
    /**
     * Seed CBC-style pathology parameters from the result-entry screenshot.
     */
    public function run(): void
    {
        $hospitalId = (int) (env('TEMP_SEED_HOSPITAL_ID') ?: (DB::table('hospitals')->value('id') ?? 0));

        if ($hospitalId <= 0) {
            $this->command?->warn('No hospital found. Set TEMP_SEED_HOSPITAL_ID in .env or create a hospital first.');
            return;
        }

        $now = now();

        $unitNames = [
            'g/dL',
            '/uL',
            '%',
            'fL',
            'pg',
            'million/uL',
            'mm/hr',
        ];

        $unitIds = [];
        foreach ($unitNames as $unitName) {
            $unitId = DB::table('pathology_units')
                ->where('hospital_id', $hospitalId)
                ->where('name', $unitName)
                ->value('id');

            if (!$unitId) {
                $unitId = DB::table('pathology_units')->insertGetId([
                    'hospital_id' => $hospitalId,
                    'name' => $unitName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $unitIds[$unitName] = $unitId;
        }

        $parameters = [
            [
                'name' => 'Haemoglobin (Hb)',
                'unit' => 'g/dL',
                'range' => 'M: 13-17 | F: 12-15',
                'applicable_gender' => 'all',
                'min_value' => 12,
                'max_value' => 17,
                'critical_low' => 7,
                'critical_high' => 20,
                'min_value_male' => 13,
                'max_value_male' => 17,
                'critical_low_male' => 8,
                'critical_high_male' => 20,
                'min_value_female' => 12,
                'max_value_female' => 15,
                'critical_low_female' => 7,
                'critical_high_female' => 18,
                'description' => 'Temporary CBC seed: gender-specific Hb ranges.',
            ],
            [
                'name' => 'WBC Count (TLC)',
                'unit' => '/uL',
                'range' => '4,000-11,000',
                'applicable_gender' => 'all',
                'min_value' => 4000,
                'max_value' => 11000,
                'critical_low' => 3000,
                'critical_high' => 20000,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'Neutrophils',
                'unit' => '%',
                'range' => '50-70%',
                'applicable_gender' => 'all',
                'min_value' => 50,
                'max_value' => 70,
                'critical_low' => 20,
                'critical_high' => 90,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'Lymphocytes',
                'unit' => '%',
                'range' => '20-40%',
                'applicable_gender' => 'all',
                'min_value' => 20,
                'max_value' => 40,
                'critical_low' => 10,
                'critical_high' => 60,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'Eosinophils',
                'unit' => '%',
                'range' => '1-6%',
                'applicable_gender' => 'all',
                'min_value' => 1,
                'max_value' => 6,
                'critical_low' => 0,
                'critical_high' => 15,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'Platelet Count',
                'unit' => '/uL',
                'range' => '1,50,000-4,00,000',
                'applicable_gender' => 'all',
                'min_value' => 150000,
                'max_value' => 400000,
                'critical_low' => 50000,
                'critical_high' => 1000000,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'PCV / Haematocrit',
                'unit' => '%',
                'range' => '36-46%',
                'applicable_gender' => 'all',
                'min_value' => 36,
                'max_value' => 46,
                'critical_low' => 20,
                'critical_high' => 60,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'MCV',
                'unit' => 'fL',
                'range' => '80-100',
                'applicable_gender' => 'all',
                'min_value' => 80,
                'max_value' => 100,
                'critical_low' => 60,
                'critical_high' => 120,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'MCH',
                'unit' => 'pg',
                'range' => '27-33',
                'applicable_gender' => 'all',
                'min_value' => 27,
                'max_value' => 33,
                'critical_low' => 20,
                'critical_high' => 40,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'MCHC',
                'unit' => 'g/dL',
                'range' => '31-37',
                'applicable_gender' => 'all',
                'min_value' => 31,
                'max_value' => 37,
                'critical_low' => 25,
                'critical_high' => 40,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'RBC Count',
                'unit' => 'million/uL',
                'range' => '4.5-5.5',
                'applicable_gender' => 'all',
                'min_value' => 4.5,
                'max_value' => 5.5,
                'critical_low' => 3.0,
                'critical_high' => 7.0,
                'description' => 'Temporary CBC seed.',
            ],
            [
                'name' => 'ESR',
                'unit' => 'mm/hr',
                'range' => 'M: 0-15 | F: 0-20',
                'applicable_gender' => 'all',
                'min_value' => 0,
                'max_value' => 20,
                'critical_low' => null,
                'critical_high' => 60,
                'min_value_male' => 0,
                'max_value_male' => 15,
                'critical_low_male' => null,
                'critical_high_male' => 60,
                'min_value_female' => 0,
                'max_value_female' => 20,
                'critical_low_female' => null,
                'critical_high_female' => 60,
                'description' => 'Temporary CBC seed: gender-specific ESR ranges.',
            ],
        ];

        $rows = [];
        foreach ($parameters as $parameter) {
            $rows[] = [
                'hospital_id' => $hospitalId,
                'pathology_unit_id' => $unitIds[$parameter['unit']] ?? null,
                'name' => $parameter['name'],
                'range' => $parameter['range'] ?? null,
                'description' => $parameter['description'] ?? null,
                'applicable_gender' => $parameter['applicable_gender'] ?? 'all',
                'min_value' => $parameter['min_value'] ?? null,
                'max_value' => $parameter['max_value'] ?? null,
                'critical_low' => $parameter['critical_low'] ?? null,
                'critical_high' => $parameter['critical_high'] ?? null,
                'min_value_male' => $parameter['min_value_male'] ?? null,
                'max_value_male' => $parameter['max_value_male'] ?? null,
                'critical_low_male' => $parameter['critical_low_male'] ?? null,
                'critical_high_male' => $parameter['critical_high_male'] ?? null,
                'min_value_female' => $parameter['min_value_female'] ?? null,
                'max_value_female' => $parameter['max_value_female'] ?? null,
                'critical_low_female' => $parameter['critical_low_female'] ?? null,
                'critical_high_female' => $parameter['critical_high_female'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('pathology_parameters')->upsert(
            $rows,
            ['hospital_id', 'name'],
            [
                'pathology_unit_id',
                'range',
                'description',
                'applicable_gender',
                'min_value',
                'max_value',
                'critical_low',
                'critical_high',
                'min_value_male',
                'max_value_male',
                'critical_low_male',
                'critical_high_male',
                'min_value_female',
                'max_value_female',
                'critical_low_female',
                'critical_high_female',
                'updated_at',
            ]
        );

        $this->command?->info('Temporary CBC pathology parameters seeded for hospital_id: ' . $hospitalId);
    }
}
