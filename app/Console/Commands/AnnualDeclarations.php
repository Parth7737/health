<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use App\Models\{Hospital, AnnualDeclaration};

class AnnualDeclarations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annual:declaration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure every hospital has an annual declaration entry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentYear = Carbon::now()->year;
        $allHospitals = Hospital::whereIn('is_empanelled', [1,2,3])->pluck('id')->toArray();

        $existingHospitals = AnnualDeclaration::where('year', $currentYear)->pluck('hospital_id')->toArray();

        $missingHospitals = array_diff($allHospitals, $existingHospitals);
        $insertData = [];
        foreach ($missingHospitals as $hospitalId) {
            $insertData[] = [
                'hospital_id'     => $hospitalId,
                'year'            => $currentYear,
                'submitted_date'  => date('Y-m-d'),
                'status'          => 0, // Pending
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ];
        }

        if (!empty($insertData)) {
            AnnualDeclaration::insert($insertData);
            $this->info(count($insertData) . " hospitals added to the declaration table.");
        } else {
            $this->info("All hospitals already have an entry for this year.");
        }
    }
}
