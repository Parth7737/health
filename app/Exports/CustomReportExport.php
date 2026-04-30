<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\PreauthRegister as P;
use Carbon\Carbon;

class CustomReportExport
{
    public function generate()
    {

        $preauthreported = P::where('state_id', 34)->count();
        $preauthapproved = P::where('state_id', 34)->whereNotNull('preauth_approved_date')->whereNot('status',P::STATUS_PREAUTH_PENDING)->count();
        $preauthrejected = P::where('state_id', 34)->where('status', P::STATUS_PREAUTH_REJECTED)->count();
        $claimnotsubmitted = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_SUBMITTED)->count();
        $preauthunderprocess = P::where('state_id', 34)->whereIn('status', [P::STATUS_PREAUTH_PENDING, P::STATUS_PREAUTH_QUERIED, P::STATUS_MEDICAL_COMMITTEE_PENDING, P::STATUS_MEDICAL_COMMITTEE_APPROVED, P::STATUS_MEDICAL_COMMITTEE_QUERIED, P::STATUS_CEO_APPROVED, P::STATUS_CEO_QUERIED, P::STATUS_ACS_PENDING, P::STATUS_ACS_APPROVED, P::STATUS_ACS_QUERIED])->count();

        $opreauthreported = P::where('state_id', '!=', 34)->count();
        $opreauthapproved = P::where('state_id', '!=', 34)->whereNotNull('preauth_approved_date')->whereNot('status',P::STATUS_PREAUTH_PENDING)->count();
        $opreauthrejected = P::where('state_id', '!=', 34)->where('status', P::STATUS_PREAUTH_REJECTED)->count();
        $oclaimnotsubmitted = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_SUBMITTED)->count();
        $opreauthunderprocess = P::where('state_id', '!=', 34)->whereIn('status', [P::STATUS_PREAUTH_PENDING, P::STATUS_PREAUTH_QUERIED, P::STATUS_MEDICAL_COMMITTEE_PENDING, P::STATUS_MEDICAL_COMMITTEE_APPROVED, P::STATUS_MEDICAL_COMMITTEE_QUERIED, P::STATUS_CEO_APPROVED, P::STATUS_CEO_QUERIED, P::STATUS_ACS_PENDING, P::STATUS_ACS_APPROVED, P::STATUS_ACS_QUERIED])->count();

        $preauthreportedsum = P::where('state_id', 34)->sum('preauth_initiated_amount');
        $preauthapprovedamount = P::where('state_id', 34)->whereNotNull('preauth_approved_date')->whereNot('status',P::STATUS_PREAUTH_PENDING)->sum('preauth_approved_amount');
        $preauthrejectedamount = P::where('state_id', 34)->where('status', P::STATUS_PREAUTH_REJECTED)->sum('preauth_initiated_amount');
        $preauthunderprocessamount = P::where('state_id', 34)->whereIn('status', [P::STATUS_PREAUTH_PENDING, P::STATUS_PREAUTH_QUERIED, P::STATUS_MEDICAL_COMMITTEE_PENDING, P::STATUS_MEDICAL_COMMITTEE_APPROVED, P::STATUS_MEDICAL_COMMITTEE_QUERIED, P::STATUS_CEO_APPROVED, P::STATUS_CEO_QUERIED, P::STATUS_ACS_PENDING, P::STATUS_ACS_APPROVED, P::STATUS_ACS_QUERIED])->sum('preauth_initiated_amount');
        $claimnotsubmittedamount = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_SUBMITTED)->sum('preauth_approved_amount');

        $opreauthreportedsum = P::where('state_id', '!=', 34)->sum('preauth_initiated_amount');
        $opreauthapprovedamount = P::where('state_id', '!=', 34)->whereNotNull('preauth_approved_date')->whereNot('status',P::STATUS_PREAUTH_PENDING)->sum('preauth_approved_amount');
        $opreauthrejectedamount = P::where('state_id', '!=', 34)->where('status', P::STATUS_PREAUTH_REJECTED)->sum('preauth_approved_amount');
        $opreauthunderprocessamount = P::where('state_id', '!=', 34)->whereIn('status', [P::STATUS_PREAUTH_PENDING, P::STATUS_PREAUTH_QUERIED, P::STATUS_MEDICAL_COMMITTEE_PENDING, P::STATUS_MEDICAL_COMMITTEE_APPROVED, P::STATUS_MEDICAL_COMMITTEE_QUERIED, P::STATUS_CEO_APPROVED, P::STATUS_CEO_QUERIED, P::STATUS_ACS_PENDING, P::STATUS_ACS_APPROVED, P::STATUS_ACS_QUERIED])->sum('preauth_initiated_amount');
        $oclaimnotsubmittedamount = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_SUBMITTED)->sum('preauth_approved_amount');

        $totalclaiminitiated = P::where('state_id', 34)->whereNotNull('claim_approved_date')->count();
        $claimpaidbybank = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_PAID_BY_BANK)->count();
        $claimrejected = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_REJECTED)->count();
        $claimpending = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CPD_CLAIM_PENDING])->count();

        $ototalclaiminitiated = P::where('state_id', '!=', 34)->whereNotNull('claim_approved_date')->count();
        $oclaimpaidbybank = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_PAID_BY_BANK)->count();
        $oclaimrejected = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_REJECTED)->count();
        $oclaimpending = P::where('state_id', '!=', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CPD_CLAIM_PENDING])->count();

        $totalclaiminitiatedamount = P::where('state_id', 34)->whereNotNull('claim_approved_date')->sum('claim_amount');
        $claimpaidbybankamount = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_PAID_BY_BANK)->sum('claim_approved_amount');
        $claimrejectedamount = P::where('state_id', 34)->where('status', P::STATUS_CLAIM_REJECTED)->sum('claim_amount');
        $claimpendingamount = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CPD_CLAIM_PENDING])->sum('claim_amount');

        $newpreauthyesterday = P::where('state_id', 34)->whereDate('preauth_submission_date', Carbon::yesterday())->count();

        $totalclaimPending = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])->count();

        $pendingatisa = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED])->count();
        $pendingatsha = P::where('state_id', 34)->whereIn('status', [P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED])->count();
        $pendingathospital = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_SUBMITTED])->count();
        $pendingatfinance = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_APPROVED,P::STATUS_ACO_CLAIM_QUERIED])->count();

        $allpendingatisa = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED])->count();
        $allpendingatsha = P::whereIn('status', [P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED])->count();
        $allpendingathospital = P::whereIn('status', [P::STATUS_CLAIM_SUBMITTED])->count();
        $allpendingatfinance = P::whereIn('status', [P::STATUS_CLAIM_APPROVED,P::STATUS_ACO_CLAIM_QUERIED])->count();

        $ototalclaiminitiatedamount = P::where('state_id', '!=', 34)->whereNotNull('claim_approved_date')->sum('claim_amount');
        $oclaimpaidbybankamount = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_PAID_BY_BANK)->sum('claim_approved_amount');
        $oclaimrejectedamount = P::where('state_id', '!=', 34)->where('status', P::STATUS_CLAIM_REJECTED)->sum('claim_amount');
        $oclaimpendingamount = P::where('state_id', '!=', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CPD_CLAIM_PENDING])->sum('claim_amount');

        $onewpreauthyesterday = P::where('state_id', '!=', 34)->whereDate('preauth_submission_date', Carbon::yesterday())->count();

        $ototalclaimPending = P::where('state_id', '!=', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])->count();

        $claimpending30days = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->where('claim_submited_date', '>=', Carbon::now()->subDays(30))
        ->count();

        $claimpending_16_30 = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(15)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(29)->endOfDay()
        ])
        ->count();

        $claimpending_10_15 = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(9)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(14)->endOfDay()
        ])
        ->count();

        $claimpending_7_9 = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(6)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(8)->endOfDay()
        ])
        ->count();

        $claimpending_1_7 = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth(),
            Carbon::now()->copy()->startOfMonth()->addDays(6)->endOfDay()
        ])
        ->count();

        $allclaimpending30days = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->where('claim_submited_date', '>=', Carbon::now()->subDays(30))
        ->count();

        $allclaimpending_16_30 = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(15)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(29)->endOfDay()
        ])
        ->count();

        $allclaimpending_10_15 = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(9)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(14)->endOfDay()
        ])
        ->count();

        $allclaimpending_7_9 = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth()->addDays(6)->startOfDay(),
            Carbon::now()->copy()->startOfMonth()->addDays(8)->endOfDay()
        ])
        ->count();

        $allclaimpending_1_7 = P::whereIn('status', [P::STATUS_CLAIM_PENDING, P::STATUS_CLAIM_QUERIED, P::STATUS_CPD_CLAIM_PENDING, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_APPROVED, P::STATUS_CLAIM_APPROVED])
        ->whereBetween('claim_submited_date', [
            Carbon::now()->copy()->startOfMonth(),
            Carbon::now()->copy()->startOfMonth()->addDays(6)->endOfDay()
        ])
        ->count();

        $claiminitiateyesterday = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_PENDING])->whereDate('claim_submited_date', Carbon::yesterday())->count();
        $claimapproved = P::where('state_id', 34)->whereIn('status', [P::STATUS_SHA_CLAIM_APPROVED])->count();
        $claimqueried = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED])->count();
        $claimRejected = P::where('state_id', 34)->whereIn('status', [P::STATUS_CLAIM_REJECTED, P::STATUS_ACO_CLAIM_REJECTED, P::STATUS_SHA_CLAIM_REJECTED])->count();

        $allclaiminitiateyesterday = P::whereIn('status', [P::STATUS_CLAIM_PENDING])->whereDate('claim_submited_date', Carbon::yesterday())->count();
        $allclaimapproved = P::whereIn('status', [P::STATUS_SHA_CLAIM_APPROVED])->count();
        $allclaimqueried = P::whereIn('status', [P::STATUS_CLAIM_QUERIED, P::STATUS_ACO_CLAIM_QUERIED, P::STATUS_SHA_CLAIM_QUERIED])->count();
        $allclaimRejected = P::whereIn('status', [P::STATUS_CLAIM_REJECTED, P::STATUS_ACO_CLAIM_REJECTED, P::STATUS_SHA_CLAIM_REJECTED])->count();

        $allclaimcpdRejected = P::where('status', P::STATUS_CLAIM_REJECTED)->count();
        $allclaimshaRejected = P::whereIn('status', [P::STATUS_ACO_CLAIM_REJECTED, P::STATUS_SHA_CLAIM_REJECTED])->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        // Title

        // first line
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'AB PMJAY ATAL AYUSHMAN UTTARAKHAND YOJANA - DAILY REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->applyFromArray($styleArray);

        // second line
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Family Health Plan Insurance TPA');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->applyFromArray($styleArray);

        $sheet->setCellValue('K2', date('d-F-y'));
        $sheet->getStyle('K2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('K2')->applyFromArray($styleArray);

        $sheet->setCellValue('M2', date('h:i A'));
        $sheet->getStyle('M2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('M2')->applyFromArray($styleArray);

        // third line
        $sheet->mergeCells('B3:C3');
        $sheet->setCellValue('B3', 'Pre-Auth Reported');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B3')->applyFromArray($styleArray);

        $sheet->mergeCells('D3:E3');
        $sheet->setCellValue('D3', 'Pre-Auth Approved');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('D3')->applyFromArray($styleArray);

        $sheet->mergeCells('F3:G3');
        $sheet->setCellValue('F3', 'Pre-Auth Rejected');
        $sheet->getStyle('F3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('F3')->applyFromArray($styleArray);

        $sheet->mergeCells('H3:I3');
        $sheet->setCellValue('H3', 'Pre-Auth Under Process');
        $sheet->getStyle('H3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('H3')->applyFromArray($styleArray);

        $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('J3', 'Preauth Approved but Claim not submitted');
        $sheet->getStyle('J3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('J3')->applyFromArray($styleArray);
        
        $sheet->setCellValue('M3', 'New Preauth Yesterday');
        $sheet->getStyle('M3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('M3')->applyFromArray($styleArray);

        $sheet->setCellValue('O3', 'UK Claims');
        $sheet->getStyle('O3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('O3')->applyFromArray($styleArray);

        $sheet->setCellValue('P3', 'Total');
        $sheet->getStyle('P3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('P3')->applyFromArray($styleArray);


        // 
        $sheet->getStyle('B4:M4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B4:M4')->applyFromArray($styleArray);

        $sheet->getStyle('A7:M7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A7:M7')->applyFromArray($styleArray);
        // Headers
        $sheet->fromArray([
            ["", "No", "Amount", "No", "Amount", "No", "Amount", "No", "Amount", "No", "Amount", "", "No", "Total Claims Pending", $this->safeValue($totalclaimPending), $this->safeValue($ototalclaimPending)],
      
            ['Uttarakhand', $this->safeValue($preauthreported), $this->safeValue($preauthreportedsum), $this->safeValue($preauthapproved), $this->safeValue($preauthapprovedamount), $this->safeValue($preauthrejected), $this->safeValue($preauthrejectedamount), $this->safeValue($preauthunderprocess), $this->safeValue($preauthunderprocessamount), $this->safeValue($claimnotsubmitted), $this->safeValue($claimnotsubmittedamount), "", $this->safeValue($newpreauthyesterday), "Pending at ISA", $this->safeValue($pendingatisa), $this->safeValue($allpendingatisa)],

            ['Other State', $this->safeValue($opreauthreported), $this->safeValue($opreauthreportedsum), $this->safeValue($opreauthapproved), $this->safeValue($opreauthapprovedamount), $this->safeValue($opreauthrejected), $this->safeValue($opreauthrejectedamount), $this->safeValue($opreauthunderprocess), $this->safeValue($opreauthunderprocessamount), $this->safeValue($oclaimnotsubmitted), $this->safeValue($oclaimnotsubmittedamount), "", $this->safeValue($onewpreauthyesterday), "Pending at SHA", $this->safeValue($pendingatsha), $this->safeValue($allpendingatsha)],

            ['Total', $this->safeValue($preauthreported+$opreauthreported), $this->safeValue($preauthreportedsum+$opreauthreportedsum), $this->safeValue($preauthapproved+$opreauthapproved), $this->safeValue($preauthapprovedamount+$opreauthapprovedamount), $this->safeValue($preauthrejected+$opreauthrejected), $this->safeValue($preauthrejectedamount+$opreauthrejectedamount), $this->safeValue($preauthunderprocess+$opreauthunderprocess), $this->safeValue($preauthunderprocessamount+$opreauthunderprocessamount), $this->safeValue($claimnotsubmitted+$oclaimnotsubmitted), $this->safeValue($claimnotsubmittedamount+$oclaimnotsubmittedamount), "", $this->safeValue($newpreauthyesterday+$onewpreauthyesterday), "Pending at Hospital", $this->safeValue($pendingathospital), $this->safeValue($allpendingathospital)],
        ], null, 'A4');

        // Styling
        $sheet->getStyle('A3:P7')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('N3:N8')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        // bottom Table

        $sheet->mergeCells('B9:C9');
        $sheet->setCellValue('B9', 'Total Claims initiated');
        $sheet->getStyle('B9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B9')->applyFromArray($styleArray);

        $sheet->mergeCells('D9:E9');
        $sheet->setCellValue('D9', 'Claims Paid by Bank');
        $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('D9')->applyFromArray($styleArray);

        $sheet->mergeCells('F9:G9');
        $sheet->setCellValue('F9', 'Claims Rejected');
        $sheet->getStyle('F9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('F9')->applyFromArray($styleArray);

        $sheet->mergeCells('H9:I9');
        $sheet->setCellValue('H9', 'Claims Under Process');
        $sheet->getStyle('H9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('H9')->applyFromArray($styleArray);

        // 
        $sheet->getStyle('A9:I10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A13:I13')->getFont()->setBold(true)->setSize(14);

        $sheet->fromArray([
            ["", "No", "Amount", "No", "Amount", "No", "Amount", "No", "Amount"],
            ['Uttarakhand', $this->safeValue($totalclaiminitiated), $this->safeValue($totalclaiminitiatedamount), $this->safeValue($claimpaidbybank), $this->safeValue($claimpaidbybankamount), $this->safeValue($claimrejected), $this->safeValue($claimrejectedamount), $this->safeValue($claimpending), $this->safeValue($claimpendingamount)],
            ['Other State', $this->safeValue($ototalclaiminitiated), $this->safeValue($ototalclaiminitiatedamount), $this->safeValue($oclaimpaidbybank), $this->safeValue($oclaimpaidbybankamount), $this->safeValue($oclaimrejected), $this->safeValue($oclaimrejectedamount), $this->safeValue($oclaimpending), $this->safeValue($oclaimpendingamount)],
            ['Total', $this->safeValue($totalclaiminitiated+$ototalclaiminitiated), $this->safeValue($totalclaiminitiatedamount+$ototalclaiminitiatedamount), $this->safeValue($claimpaidbybank+$oclaimpaidbybank), $this->safeValue($claimpaidbybankamount+$oclaimpaidbybankamount), $this->safeValue($claimrejected+$oclaimrejected), $this->safeValue($claimrejectedamount+$oclaimrejectedamount), $this->safeValue($claimpending+$oclaimpending), $this->safeValue($claimpendingamount+$oclaimpendingamount)],
        ], null, 'A10');

        $sheet->getStyle('A9:I13')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
     
        // 
        $sheet->fromArray([
            ["Pending at Finance", $this->safeValue($pendingatfinance), $this->safeValue($allpendingatfinance)],
            ["Claims Pending >30 days", $this->safeValue($claimpending30days), $this->safeValue($allclaimpending30days)],
            ["Claims Pending 16-30 days", $this->safeValue($claimpending_16_30), $this->safeValue($allclaimpending_16_30)],
            ["Claims Pending 10-15 days", $this->safeValue($claimpending_10_15), $this->safeValue($allclaimpending_10_15)],
            ["Claims Pending 7-9 days", $this->safeValue($claimpending_7_9), $this->safeValue($allclaimpending_7_9)],
            ["Claims Pending 1-7 days", $this->safeValue($claimpending_1_7), $this->safeValue($allclaimpending_1_7)],
            ["", "", ""],
            ['Claims initiated yesterday', $this->safeValue($claiminitiateyesterday), $this->safeValue($allclaiminitiateyesterday)],
            ["Claims Processed", 360, 360],
            ['Approved', $this->safeValue($claimapproved), $this->safeValue($allclaimapproved)],
            ['Query', $this->safeValue($claimqueried), $this->safeValue($allclaimqueried)],
            ['Rejected', $this->safeValue($claimRejected), $this->safeValue($allclaimRejected)],
        ], null, 'N8');

        $sheet->getStyle('N9:P10')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('N8:P19')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('N8:N19')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // 
        $sheet->fromArray([
            ["", "Total Cards Verified", "New Inflow of Cards", "Cards Approved Today", "Total Cards Approved", "Cards Rejected", "Cards Pending At ISA", "Cards Pending At SHA"],
            ['FHPL', 3882526, 9, 7, 3882492, 52012, 0, 34],
        ], null, 'A16');

        $sheet->getStyle('A16:H16')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('A16:H17')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        
        // ---------------------- //
        $sheet->mergeCells('E20:F21');
        $sheet->mergeCells('E22:F22');
        $sheet->mergeCells('J20:K20');
        $sheet->mergeCells('M20:N20');
        $sheet->mergeCells('J21:J22');
        $sheet->mergeCells('K21:K22');
        $sheet->fromArray([
            ["CPD Reject", $this->safeValue($allclaimcpdRejected), "PPD Approved/Query Cases Terminated by SHA","", "No", "Amount", "Assigned Cases at ISA", "Pending at SHA", "", "NTMS", "Pending at Finance", "", "UK", "NTMS"],
            ['SHA Reject', $this->safeValue($allclaimshaRejected), "", "", 2257, 17912230, 9, "Claim Payment Rinitiated by ACO", 0, "", "CPD Approved", "", 541, 43],
            ['Total Reject', $this->safeValue($allclaimcpdRejected+$allclaimshaRejected), "Actual Preauth Approved", "", 188609, 1714697062,"", "", "", "", "Claim Sent to Bank", "", 0, 0],
        ], null, 'C20');

        $sheet->getStyle('E20:P20')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C22:E22')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('C20:H22')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true, // ✅ Enables text wrapping
            ],
        ]);

        $sheet->getStyle('I20:I21')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true, // ✅ Enables text wrapping
            ],
        ]);

        $sheet->getStyle('J20:P22')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true, // ✅ Enables text wrapping
            ],
        ]);

        // ------------------ //
        $sheet->mergeCells('J23:J24');
        $sheet->mergeCells('K23:K24');
        $sheet->mergeCells('L23:L24');
        $sheet->mergeCells('M23:N23');
        $sheet->mergeCells('M24:N24');
        $sheet->mergeCells('M25:N25');
        $sheet->mergeCells('M26:N26');
        $sheet->fromArray([
            ["ACO Forwarded", 551, 2,"Claim Ack received by Bank","", 267, 0],
            ["", "", "","Claim Reject by Bank", "", 1, 0],
            ["Claim Approved by", 0, "", "Claim Ready for Payment","", 1, 0],
            ['Total', 551, 2, "Total", "", 809, 43],
        ], null, 'J23');

        $sheet->getStyle('J26:P26')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('J23:P26')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true, // ✅ Enables text wrapping
            ],
        ]);
        // ------------------ //

        // for ($i = 3; $i <= 7; $i++) {
        //     $sheet->getRowDimension($i)->setRowHeight(-1);
        // }

        // foreach (range('A', 'P') as $col) {
        //     $sheet->getColumnDimension($col)->setAutoSize(true);
           
        //     // $sheet->getRowDimension(3)->setRowHeight(-1);
        //     // $sheet->getStyle($col)->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        // }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setWidth(17);
            $sheet->getStyle($col)->applyFromArray($styleArray);
        }
        // Save to temporary file
        $fileName = 'uttarakhand_report.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return $tempFile;
    }

    public function safeValue($value) {
        return is_scalar($value) ? $value : (is_null($value) ? '0' : (string) $value);
    }
}
