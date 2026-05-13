<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\PreauthRegister;

class CaseReportData
{
    public function generate($data, $status)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        
        // Merge and style report title
        $sheet->mergeCells('A1:O2');
        $sheet->setCellValue('A1', $status . ' Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->applyFromArray($styleArray);
        $sheet->getStyle('A4:O4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:O2')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => $styleArray['alignment'],
        ]);
        // Table header
        $headerarr = ["No", "Register Id", 'Card Id', "Care Plan", "Patient Name", "Age", "Gender", "Mobile No", "Preauth Initiated Amount", "Preauth Approved Amount", "Claim Approved Amount", "Erroneous Raise Amount", "Erroneous Approved Amount", "Current Status", "Registration Date"];
        $rowarray = [$headerarr];
        
        // Table body
        foreach ($data as $key => $value) {
            $rowarray[] = [
                $key + 1,
                @$value->register_id,
                @$value->benificiary->card_id,
                @$value->benificiary->care_plan,
                @$value->benificiary->name,
                @$value->benificiary->age . ' Yr',
                @$value->benificiary->gender,
                @$value->benificiary->mobile_no,
                @$value->preauth_initiated_amount ?number_format(@$value->preauth_initiated_amount,2): '0',
                @$value->preauth_approved_amount ?number_format(@$value->preauth_approved_amount,2): '0',
                @$value->claim_approved_amount ?number_format(@$value->claim_approved_amount,2): '0',
                @$value->erroneous_raise_amount ?number_format(@$value->erroneous_raise_amount,2): '0',
                @$value->erroneous_appoved_amount ?number_format(@$value->erroneous_appoved_amount,2): '0',
                @$value->status_label ?? '',
                date("d/m/Y h:i A",strtotime($value->created_at)) ?? '',
            ];
        }
        
        // Write data to sheet starting from A3
        $sheet->fromArray($rowarray, null, 'A4');
        
        // Calculate range dynamically
        $totalRows = count($rowarray) + 3;
        $lastColumn = 'O';
        $range = "A4:{$lastColumn}{$totalRows}";
        
        // Apply border and alignment to the full data range
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => $styleArray['alignment'],
        ]);
        
        // ------------------ //

        // for ($i = 3; $i <= 7; $i++) {
        //     $sheet->getRowDimension($i)->setRowHeight(-1);
        // }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
           
            // $sheet->getRowDimension(3)->setRowHeight(-1);
            // $sheet->getStyle($col)->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Save to temporary file
        $fileName = $status.'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return $tempFile;
    }
}
