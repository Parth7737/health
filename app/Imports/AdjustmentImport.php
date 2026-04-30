<?php 
namespace App\Imports;

use App\Models\Adjustment;
use App\Models\PreauthRegister;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class AdjustmentImport
{
    public function import($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $inserted = 0;
        $invalid = 0;
        $already = 0;
        $batchData = [];
        $batchSize = 1000; // Insert records in batches of 1000

        // Get all register_ids at once to reduce queries
        $registerIds = array_column($rows, 0);
        $existingPreauths = PreauthRegister::whereIn('register_id', $registerIds)
            ->pluck('id', 'register_id')
            ->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row
            }

            $registerId = $row[0] ?? null;
            $adjustmentAmount = $row[1] ?? null;
            $isOfflinePayment = $row[2] ?? null;
            $remarks = $row[3] ?? null;
            $utrNumber = $row[4] ?? null;
            $transactionDate = isset($row[5]) ? date("Y-m-d", strtotime($row[5])) : null;

            if (!isset($existingPreauths[$registerId])) {
                $invalid++;
                continue;
            }

            $preauthId = $existingPreauths[$registerId];

            // Check if Adjustment already exists
            $adjustmentExists = Adjustment::where('preauth_register_id', $preauthId)->exists();
            if ($adjustmentExists) {
                $already++;
                continue;
            }

            // Add to batch
            $batchData[] = [
                'preauth_register_id' => $preauthId,
                'hospital_id' => PreauthRegister::where('id', $preauthId)->value('hospital_id'),
                'adjustment_amount' => $adjustmentAmount,
                'is_offline_payment' => $isOfflinePayment,
                'remarks' => $remarks,
                'status' => $isOfflinePayment === 'N' ? Adjustment::STATUS_PENDING : Adjustment::STATUS_PAID,
                'utr_number' => $utrNumber,
                'transaction_date' => $transactionDate,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $inserted++;

            // Insert in batches
            if (count($batchData) >= $batchSize) {
                DB::table('adjustments')->insert($batchData);
                $batchData = []; // Reset batch array to free memory
            }
        }

        // Insert remaining records if any
        if (!empty($batchData)) {
            DB::table('adjustments')->insert($batchData);
        }

        return [
            'inserted' => $inserted,
            'invalid' => $invalid,
            'already' => $already,
        ];
    }
}
